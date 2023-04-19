<?php

namespace Pk\Common\Database;

use Tightenco\Collect\Support\Collection;
use WP_Post;

defined('ABSPATH') || exit;

trait InteractsWithWP {

    /**
     * Only Fields
     * 
     * It helps to retrieve only an
     * specific list of fields if
     * necessary
     *
     * @var array
     */
    protected $only_fields = array();

    /**
     * Save the instance
     *
     * @param $unslash - Define if want to unslash content
     * @return self
     */
    public function save($unslash = false) {
        $this->performSave($unslash);

        return $this;
    }

    /**
     * Update the instance
     *
     * @param array $attributes
     * @return self
     */
    public function update(array $attributes = array()) {
        if ($attributes) {
            $this->setProps($attributes);
        }

        $this->performSave();

        return $this;
    }

    /**
     * Create a new element
     *
     * @param array $attributes
     * @return self
     */
    public static function create(array $attributes = array()) {
        $model = new static($attributes);
        $model->save();

        $model->afterCreate();

        return $model->refresh();
    }

    /**
     * Execute a WP_Query
     *
     * @param array $args
     * @param array $only_fields
     * @return Collection
     */
    public static function where(
        array $args = array(),
        array $only_fields = array()
    ) {
        $instance   = new static;
        $query_args = $instance->parseArguments($args);
        $posts      = get_posts($query_args);

        // We're going to convert the posts to a collection
        $posts = Collection::make($posts)->map(function ($post) use ($only_fields) {
            $_post = new static((array) $post);
            $_post->setOnlyFields($only_fields);
            $_post->refreshUsing($post);

            return $_post;
        });

        return $posts;
    }

    /**
     * It basically runs a WP_Query to be able to retrieve pagination stuff
     *
     * @param array $args
     * @return Paginator
     */
    public static function paginate(array $args = array()): Paginator {
        return new Paginator(static::class, $args);
    }

    /**
     * Retrieve all records
     *
     * @return Collection
     */
    public static function all() {
        return self::where(array('posts_per_page' => 50));
    }

    /**
     * Get element by object id
     *
     * @param string|integer $object_id
     * @return self|false
     */
    public static function getByObjectId($object_id) {
        if (!$object_id) {
            return false;
        }
        $object = self::where([
            'post_type' => static::POST_TYPE['id'],
            'object_id' => $object_id,
            'post_status' => ['publish', 'draft', 'inherit']
        ]);

        return $object->isNotEmpty()
            ? $object->first()
            : false;
    }

    /**
     * Find a model
     *
     * @param int|string|WP_Post $object_id
     * @return self|null
     */
    public static function find($object_id) {
        if ($object_id instanceof self) {
            return $object_id;
        }

        $object = get_post($object_id);

        if (!$object) {
            return null;
        }

        $model = new static(array('ID' => $object->ID));
        $model->refreshUsing($object);

        return $model;
    }

    /**
     * Parse arguments and convert it in a classic WP_Query args params
     *
     * @param array $args
     * @return array
     */
    public function parseArguments(array $args = array()): array {
        $query_args = wp_parse_args($args, array(
            'posts_per_page' => 20,
            'post_status'    => 'publish',
            'post_type'      => $this::POST_TYPE['id'],
        ));

        $meta_query = array();
        foreach (array_keys($args) as $argument) {
            if ($this->isProp($argument)) {
                $meta_query[] = array(
                    'key'     => $argument,
                    'value'   => $this->valueToStore(
                        $argument,
                        $query_args[$argument]
                    ),
                    'compare' => '='
                );
            }
        }

        if ($meta_query) {
            $meta_query['relation']   = 'AND';
            $query_args['meta_query'] = $meta_query;
        }

        return $query_args;
    }

    /**
     * Peform saving
     *
     * @param $unslash - Define if want to unslash content
     * @return integer
     */
    protected function performSave($unslash = false): int {
        $meta_fields = []; // or custom fields
        $args        = [
            'post_type' => $this->getPostTypeSlug()
        ];

        $this->beforeSave();

        // This will throw an error if fails
        $this->validateProps();

        foreach ($this->wp_fields as $key => $value) {
            $args[$key] = $this->valueToStore($key, $value);
        }

        if ($this->data) {
            foreach ($this->data as $key => $value) {
                $meta_fields[$key] = $this->valueToStore($key, $value);
            }

            if (!$this->usingAcf()) {
                $args['meta_input'] = $meta_fields;
            }
        }

        if ($unslash) {
            $args = wp_unslash($args);
        }

        $post_id = wp_insert_post($args);

        // Set the post id if it's zero.
        // It means that the action is creating.
        if ($args['ID'] === 0) {
            $this->setProp('ID', $post_id);
        }

        if ($this->usingAcf()) {
            foreach ($meta_fields as $key => $value) {
                update_field($key, $value, $post_id);
            }
        }

        return $post_id;
    }

    /**
     * Fires actions before save the object
     *
     * @return void
     */
    protected function beforeSave(): void {
    }

    /**
     * Fires actions after create the object
     */
    protected function afterCreate(): void {
    }

    /**
     * Is using advanced custom fields?
     *
     * @return boolean
     */
    protected function usingAcf(): bool {
        return false;
    }

    /**
     * Set Only Fields needed
     *
     * @param array $only_fields
     * @return self
     */
    public function setOnlyFields(array $only_fields = array()): self {
        $this->only_fields = $only_fields;

        return $this;
    }

    /**
     * Refresh content from database
     *
     * @param WP_Post|null $object
     * @return self|null
     */
    public function refresh($object = null) {
        /**
         * Obviously to refresh an isntance is necessary have an ID.
         * Otherwise it will return null
         */
        if ($this->ID === 0) {
            return null;
        }

        // Retrieve the post updated as array
        $object = $object ? (array) $object : get_post($this->ID, ARRAY_A);

        // Return null if it does not exists
        if (!$object) {
            return null;
        }

        // Fill the wp fields first
        $this->fill($object);

        // Now let us take a look on post meta fields
        if ($this->data) {
            foreach ($this->data as $key => $value) {
                if (!$this->shouldRetrieveData($key)) {
                    continue;
                }

                $this->data[$key] = $this->usingAcf()
                    ? get_field($key, $this->ID)
                    : get_post_meta($this->ID, $key, true);
            }
        }

        return $this;
    }

    /**
     * Refresh using a post instance
     *
     * @param WP_Post $post
     * @return self
     */
    public function refreshUsing(WP_Post $post) {
        return $this->refresh($post);
    }

    /**
     * Should Retrieve data of tis fields?
     *
     * @param string $field
     * @return boolean
     */
    protected function shouldRetrieveData(string $field) {
        if (count($this->only_fields) === 0) {
            return true;
        }

        return in_array($field, $this->only_fields);
    }

    /**
     * Update only a data using update_post_meta
     *
     * @param string $prop
     * @param mixed $value
     * @return bool|self
     */
    public function updateData(string $prop, $value) {
        if ($this->isNotProp($prop)) {
            return false;
        }
        $value = $this->valueToStore($prop, $value);
        update_post_meta($this->ID, $prop, $value);
        return $this;
    }

    /**
     * Retrieve an instance previous saved in instance.
     *
     * @param Model|User|null|int $attribute
     * @param mixed $instance
     * @return null|Model
     */
    public function belongsTo($instance, $attribute = null) {
        if (!method_exists($instance, 'find')) {
            throw new Exception('find() method should be avaliable in' . $instance::class);
        }

        $attribute = $this->getAttributeByClassName($instance, $attribute);
        // We get the value assigned for the relational attribute
        $value = $this->{$attribute};
        if (!$value) { // If null or zero, return null.
            return null;
        }

        // If the relation hasn't been saved before, load it.
        if (!array_key_exists($attribute, $this->belongs_to_fields)) {
            $this->belongs_to_fields[$attribute] = $instance::find($value);
        }

        // return the saved value
        return $this->belongs_to_fields[$attribute];
    }

    /**
     * Has Many Relationship
     *
     * @param Model|User $instance
     * @param mixed $attribute
     * @return Collection
     */
    public function hasMany($instance, $attribute = null): Collection {
        if (!method_exists($instance, 'where')) {
            throw new Exception('where() method should be avaliable in' . $instance::class);
        }

        $attribute = $this->getAttributeByClassName($instance, $attribute);
        $value     = $this->{$attribute};
        // $value needs to be an array or contains at least a value.
        if (!$value || !is_array($value)) { // if null or zero or an empty array, return an empty array.
            return Collection::make([]);
        }

        if (!array_key_exists($attribute, $this->has_many_fields)) {
            $this->has_many_fields[$attribute] = $instance::where([
                'post__in' => $value
            ]);
        }

        return $this->has_many_fields[$attribute];
    }

    public function delete() {
        wp_delete_post($this->ID, true);
    }
}
