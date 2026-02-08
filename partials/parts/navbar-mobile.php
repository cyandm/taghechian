<?php

use Cyan\Theme\Helpers\Icon;

$navbar_mobile = [];
for ($i = 1; $i <= 4; $i++) {
    $navbar_mobile[] = [
        'icon' => get_option('navbar_mobile_icon_' . $i),
        'text' => get_option('navbar_mobile_text_' . $i),
        'link' => get_option('navbar_mobile_link_' . $i)
    ];
}
?>

<section class="flex justify-between items-center w-full sm:hidden sm:pointer-events-none bg-white/50 backdrop-blur-sm py-2.5 px-5 fixed bottom-0 left-0 right-0">

    <?php foreach ($navbar_mobile as $navbar_mobile_item) : ?>
        <a href="<?php echo $navbar_mobile_item['link'] ?>" class="flex flex-col items-center gap-1 py-1 px-6 hover:bg-cynYellow active:bg-cynYellow rounded-lg transition-all duration-300">
            <i class="size-6 [&_svg]:stroke-[1.5] flex items-center justify-center text-cynBlack">
                <?php Icon::print($navbar_mobile_item['icon']) ?>
            </i>
            <span class="text-xs font-medium text-cynBlack whitespace-nowrap"><?php echo $navbar_mobile_item['text'] ?></span>
        </a>
    <?php endforeach; ?>

</section>