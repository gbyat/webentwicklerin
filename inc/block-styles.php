<?php
add_action('init', 'webethm_block_styles');
function webethm_block_styles()
{
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
