<a href="<?php the_permalink(); ?>" class="blog-card relative">
    <div class="">
        <?php the_post_thumbnail('full', ['class' => 'object-cover w-full h-[380px]']); ?>
    </div>
    <div class="absolute bottom-0 left-0 right-0 p-4 text-white flex flex-col gap-3 justify-end h-full bg-linear-cart">
        <p class="text-xl font-semibold"><?php the_title(); ?></p>
        <p class=" text-base font-light line-clamp-1"><?php echo get_the_excerpt(); ?></p>
    </div>
</a>