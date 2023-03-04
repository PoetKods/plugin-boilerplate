<?php
/**
 * PK
 *
 * @package   pk
 * @author    PoetKods <david@poetkods.com>
 * @copyright 2023 PK
 * @license   MIT
 * @link      https://poetkods.com
 */
?>
<p>
    <?php
    /**
     * @see \Pk\App\Frontend\Templates
     * @var $args
     */
    echo __( 'This is being loaded inside "wp_footer" from the templates class', 'pk' ) . ' ' . $args[ 'data' ][ 'text' ];
    ?>
</p>
