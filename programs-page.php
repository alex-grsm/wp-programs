<?php
/* Template Name: Programs Page */

get_header();

?>

<div class="ej-programs">

    <div class="ej-programs__head">
        <div class="nm-post-thumbnail">
            <?php if ( has_post_thumbnail() ) {
                $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                echo '<img src="' . $thumbnail[0] . '" alt="Banner">';
            } ?>
            <div class="nm-post-content">
                <h1></h1>
                <h1 class="nm-post-title"><?php the_title(); ?></h1>
                <p class="nm-post-subtitle"><?php the_field('programs_description'); ?></p>
            </div>
        </div>
    </div>

    <div class="ej-programs__content">
        <div class="ej-programs__container">

            <?php
                $parent_terms = get_terms(
                    array(
                        'taxonomy' => 'category',
                        'hide_empty' => false,
                        'parent' => get_category_by_slug('programme-1')->term_id,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    )
                );

                usort($parent_terms, function($a, $b) {
                    return strnatcasecmp($a->name, $b->name);
                });

                if ($parent_terms) :
                    echo '<div class="programs-list">';
                    foreach ($parent_terms as $parent_term) :
                        // выводим название родительской категории 
                        echo '<div class="programs-list__item">';
                        echo '<span data-term-id="' . $parent_term->term_id . '">' . $parent_term->name . '</span>';
                        echo '</div>';

                        // выводим все дочерние категории первого порядка
                        $child_terms = get_terms(
                            array(
                                'taxonomy' => 'category',
                                'hide_empty' => false,
                                'parent' => $parent_term->term_id,
                                'orderby' => 'name',
                                'order' => 'ASC'
                            )
                        );

                        if ($child_terms) :
                            echo '<ul class="programs-tags">';
                            foreach ($child_terms as $child_term) :
                                echo '<li class="programs-tags__item">';
                                echo '<span data-term-id="' . $child_term->term_id . '">' . $child_term->name . '</span>';
                                echo '</li>';
                            endforeach;
                            echo '</ul>';
                        endif;
                    endforeach;
                    echo '</div>';
                endif;
            ?>

            <div class="program-box__wrap" style="opacity:1;">
                <!-- program box start -->
                <div class="program-box">
                    <div class="program-box__head">
                        <h2 class="program-box__title">Warm Up</h2>
                        <div class="program-box__rounds">2 - 3 Runden</div>
                    </div>
                    <div class="program-box__content">
                        <div class="swiper programSwiper">
                            <div class="swiper-wrapper" id="warmUpBox">
                                <?php get_posts_html('woche-1', 'tag-1', 'warm-up'); ?>
                            </div>
                            <?php swiperNav(); ?>
                        </div>
                    </div>
                </div>
                <!-- program box start -->
                <div class="program-box">
                    <div class="program-box__head">
                        <h2 class="program-box__title">Hauptteil</h2>
                        <div class="program-box__rounds" id="Hauptteil-Runden">3-5 Runden</div>
                    </div>
                    <div class="program-box__head-under">
                        <div class="squats" id="Hauptteil-LeftText">EMOM Every Minute On The Minute</div>
                        <div class="squats-text" id="Hauptteil-RightText"></div>
                    </div>
                    <div class="program-box__content">
                        <div class="swiper programSwiper">
                            <div class="swiper-wrapper" id="hauptteilBox">
                                <?php get_posts_html('woche-1', 'tag-1', 'hauptteil'); ?>
                            </div>
                            <?php swiperNav(); ?>
                        </div>
                    </div>
                </div>
                <!-- program box start -->
                <div class="program-box">
                    <div class="program-box__head">
                        <h2 class="program-box__title">Cool Down</h2>
                        <div class="program-box__rounds">1 Runde</div>
                    </div>
                    <div class="program-box__content">
                        <div class="swiper programSwiper">
                            <div class="swiper-wrapper" id="coolDownBox">
                                <?php get_posts_html('woche-1', 'tag-1', 'cool-down'); ?>
                            </div>
                            <?php swiperNav(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
  jQuery(document).ready(function($) {
    const swiperSettings = {
        spaceBetween: 30,
        speed: 500,
        freeMode: true,
        // grabCursor: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            0: {
                centeredSlides: true,
                slidesPerView: 1.5,
                slidesOffsetBefore: -75,
            },
            768: {
                slidesPerView: 3,
            },
        },
    };  

    const programSwipers = Array.from(document.querySelectorAll('.programSwiper'));
    programSwipers.forEach(programSwiper => {
        const mySwiper = new Swiper(programSwiper, swiperSettings);
        programSwiper.swiperInstance = mySwiper;
    });

    const updateProgramSwipers = () => {
        programSwipers.forEach((programSwiper) => programSwiper.swiperInstance.update());
    };


function getCategoriesBySearchTerm(searchTerm) {
  $.ajax({
    url: '/wp-json/wp/v2/categories',
    method: 'GET',
    data: {
      per_page: 100,
      search: searchTerm,
    },
    success: function(response) {
    // console.log(response);
      response.forEach(category => {
        const nameWithUnderscores = category.name.replace(/\s+/g, '_');
        categoryMapping.push({ 
            id: category.id, 
            name: nameWithUnderscores, 
            runden: category.acf.runden,
            left_text: category.acf.left_text,
            right_text: category.acf.right_text,
            parent: category.parent,
        });
      });
    },
    error: function(error) {
      console.log(error);
    }
  });
}

const categoryMapping = [];
// console.log(categoryMapping);
getCategoriesBySearchTerm('Warm Up');
getCategoriesBySearchTerm('Hauptteil');
getCategoriesBySearchTerm('Cool Down');

/** Filters */
const $programsList = $('.programs-list');

const $warmUpBox = $('#warmUpBox'),
      $hauptteilBox = $('#hauptteilBox'),
      $coolDownBox = $('#coolDownBox');

const $programsListItem = $('.programs-list__item'),
      $programsTagsItem = $('.programs-tags__item');

const $hauptteilRunden = $('#Hauptteil-Runden'),
      $hauptteilLeftText = $('#Hauptteil-LeftText'),
      $hauptteilRightText = $('#Hauptteil-RightText');

// Weeks filter
$programsListItem.on('click', function() {
    const $this = $(this);
    const $programsTags = $this.next('.programs-tags');

    $programsListItem.not($this).removeClass('active');
    $this.toggleClass('active');
    $('.programs-tags.show').not($programsTags).removeClass('show');
    $programsTags.toggleClass('show');
    $programsTags.children().removeClass('active').first().addClass('active');

    const termId = $this.children().first().data('term-id');

    getPostsByCategory(termId).then((data) => {
        console.log(data);
        if (data.length) {
            const content = {
                Warm_Up: '',
                Hauptteil: '',
                Cool_Down: '',
            };

            data.forEach((post) => {
                const categories = post.categories;
                const category = categories.find((category) => categoryMapping.some((item) => item.id === category));

                if (category) {
                    const categoryName = categoryMapping.find((item) => item.id === category)?.name;
                    
                    if (categoryName) {
                        content[categoryName] += slideTemplate(post);
                    }
                }
            });

            updateContent(content);
        }
    }).then(() => {
        $programsTags.children().first().trigger('click');
    }).catch((error) => {
        console.log(error);
    });

});

// Days filter
$programsTagsItem.on('click', function() {
    const $this = $(this);
    $programsTagsItem.removeClass('active');
    $this.toggleClass('active');

    const termId = $this.children().first().data('term-id');
    let hauptteilRunden = '';
    let hauptteilLeftText = '';
    let hauptteilRightText = '';

    getPostsByCategory(termId).then((data) => {
        console.log(data);
        if (data.length) {
            const content = {
                'Warm_Up': '',
                'Hauptteil': '',
                'Cool_Down': ''
            };
            
            data.forEach((post) => {
                const categories = post.categories;

                categories.forEach((category) => {
                    const name = categoryMapping.find((item) => item.id === category)?.name;
                    const categoryObject = categoryMapping.find((item) => item.id === category);

                    if (name && categoryObject && categoryObject.parent === termId) {
                        if (categoryObject.name === 'Hauptteil' && !hauptteilRunden) {
                            hauptteilRunden = categoryObject.runden;
                            hauptteilLeftText = categoryObject.left_text;
                            hauptteilRightText = categoryObject.right_text;
                        }

                        content[name] += slideTemplate(post);
                    }
                });
            });

            $hauptteilRunden.text(hauptteilRunden || '');
            $hauptteilLeftText.text(hauptteilLeftText || '');
            $hauptteilRightText.text(hauptteilRightText || '');
            updateContent(content);
        }
    }).catch((error) => {
        console.log(error);
    });
});


function getPostsByCategory(categoryId) {
  return $.ajax({
    url: '/wp-json/wp/v2/posts',
    data: {
      categories: categoryId,
      per_page: 100,
      orderby: 'date',
      order: 'asc',
    },
  });
}

function slideTemplate(post) {
    return `
        <div class="swiper-slide">
            <div class="programSwiper__item">
                <div class="programSwiper__item-thumbnail">
                    <iframe src="${post.acf.video_url}&title=0&byline=0" frameborder="0" allowfullscreen></iframe>
                </div>
                <div class="programSwiper__item-name">${post.title.rendered}</div>
            </div>
        </div>
    `;
}

function updateContent(content) {
  $warmUpBox.html(content['Warm_Up']);
  $hauptteilBox.html(content['Hauptteil']);
  $coolDownBox.html(content['Cool_Down']);
  updateProgramSwipers();
}

    setTimeout(() => {
        // $('.programs-list > .programs-list__item:first-of-type').addClass('active');
        // $('.programs-list > .programs-tags:first-of-type').addClass('show');
        // $('.programs-list > .programs-tags li:first-of-type').addClass('active');
        // $('.program-box__wrap').css('opacity', '1');
        // if ($('.programs-tags.show')) {
        //     $('.programs-tags.show .programs-tags__item.active').trigger('click');
        // }
        $programsList
            .find('.programs-list__item:first-of-type')
            .addClass('active')
            .end()
            .find('.programs-tags:first-of-type')
            .addClass('show')
            .find('li:first-of-type')
            .addClass('active')
            .end()
            .end()
            .find('.program-box__wrap')
            .css('opacity', '1');
        
        // const $activeTags = $programsList.find('.programs-tags.show .programs-tags__item.active');
        // if ($activeTags.length) {
        //     $activeTags.trigger('click');
        // }
    }, 200);

    
  });
</script>


<?php 

function swiperNav() {
    echo '<div class="swiper-button-prev">
            <img src="' . get_stylesheet_directory_uri() . '/img/circle-left-regular.svg" alt="←">
          </div>
          <div class="swiper-button-next">
            <img src="' . get_stylesheet_directory_uri() . '/img/circle-right-regular.svg" alt="→">
          </div>';
}

function get_posts_html($week, $day, $cat) {
    $warmup_posts = get_posts( array(
        'category_name' => 'programme-1',
        'post_type' => 'post',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $week,
                'include_children' => true
            ),
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $day,
                'include_children' => true
            ),
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $cat,
                'include_children' => false
            )
        ),
        'orderby' => 'date',
        'order' => 'ASC',
        'post_type' => 'post',
        'suppress_filters' => true
    ) );

    global $post;

    foreach( $warmup_posts as $post ){
        setup_postdata( $post );
        ?>
            <div class="swiper-slide">
                <div class="programSwiper__item">
                    <div class="programSwiper__item-thumbnail">
                        <iframe src="<?= the_field('video_url'); ?>&title=0&byline=0" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div class="programSwiper__item-name"><?php the_title(); ?></div>
                </div>
            </div>
        <?php
    }
    wp_reset_postdata();
}

get_footer(); 

?>
