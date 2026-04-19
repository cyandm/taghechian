<?php /* Template Name: Home */ ?>

<?php

use Cyan\Theme\Helpers\Templates;

get_header(); ?>

<main class="relative">

    <?php Templates::getPart('home/slider'); ?>
    <?php Templates::getPart('home/about'); ?>
    <?php Templates::getPart('home/cats'); ?>
    <?php Templates::getPart('home/sales'); ?>
    <?php Templates::getPart('home/show-one'); ?>
    <?php Templates::getPart('home/banner-double'); ?>
    <?php Templates::getPart('home/banner'); ?>
    <?php Templates::getPart('home/show-two'); ?>
    <?php Templates::getPart('home/popular'); ?>
    <?php Templates::getPart('home/show-three'); ?>
    <?php Templates::getPart('home/events'); ?>

</main>

<?php get_footer(); ?>