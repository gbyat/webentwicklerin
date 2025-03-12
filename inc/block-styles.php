<?php
add_action('init', 'webthm_block_styles');
function webthm_block_styles()
{

    /**
     * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
     * for a specific block. These will only get loaded when the block is rendered
     * (both in the editor and on the front end), improving performance
     * and reducing the amount of data requested by visitors.
     *
     * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
     */

    register_block_style(
        'core/group',
        array(
            'name'         => 'clipped-background',
            'label'        => __('Clipped Background', 'webentwicklerin'),

            'inline_style' => '.is-style-clipped-background{
            -webkit-clip-path: polygon(10% 10%, 46% 1%, 44% 15%, 100% 0, 95% 90%, 31% 100%, 32% 90%, 0 100%);
            clip-path: polygon(10% 10%, 46% 1%, 44% 15%, 100% 0, 95% 90%, 31% 100%, 32% 90%, 0 100%);}
            .is-style-clipped-background a {
            text-decoration:none;}',
        )
    );

    register_block_style(
        'core/group',
        array(
            'name'         => 'animated-border',
            'label'        => __('Animated border', 'webentwicklerin'),

            'inline_style' => '.is-style-animated-border{
            position: relative;
            padding: 5px 0 0 0;
            box-sizing: border-box;
            overflow: hidden;
            animation: color-animation 50s linear infinite;
            }
            .is-style-animated-border::before{
            content: "";
            display: block;
            background: linear-gradient(90deg, var(--wp--preset--color--accent) 30%, transparent 30%, transparent 75%, var(--wp--preset--color--accent-2) 75%);
            background-size: 20px;
            position: absolute;
            top: -1000px;
            left: -50px;
            bottom: -1000px;
            right: -50px;
            transform: rotate(45deg);
            overflow: hidden; }
            @keyframes gradient-animation {
                0% {
                    background-position: 0% 50%;
                }

                100% {
                    background-position: 100% 50%;
                }
            }

            @keyframes color-animation {
                0% {
                    background-color: var(--wp--preset--color--accent);
                }

                50% {
                    background-color: var(--wp--preset--color--accent-3);
                }

                100% {
                    background-color: var(--wp--preset--color--accent);
                }
            }'

        )
    );
    /**
     *             animation: gradient-animation 500s linear infinite;
     */
    register_block_style(
        'core/list',
        array(
            'name'         => 'colored-bullets',
            'label'        => __('Colored Bullets', 'webentwicklerin'),

            'inline_style' => '
            ul.is-style-colored-bullets {
                list-style: none;
            }

            ul.is-style-colored-bullets > li::before {
                content: "\2022";
                color: var(--wp--preset--color--accent);
                font-weight: bold;
                display: inline-block;
                width: 0.75em;
                margin-left: -0.75em;
            }
            ul.is-style-colored-bullets ul li::before {
                content: "\29BF";
                color: var(--wp--preset--color--accent);
                font-weight: bold;
                display: inline-block;
                width: 0.75em;
                margin-left: -0.75em;
            }
            ul.is-style-colored-bullets > li > div,
            ul.is-style-colored-bullets ul li > div{
                display: inline-block;
            }',
        )
    );
}
