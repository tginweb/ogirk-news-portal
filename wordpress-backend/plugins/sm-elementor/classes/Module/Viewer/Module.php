<?php


namespace SM_Elementor\Module\Viewer;

use Elementor\Controls_Stack;
use ElementorPro\Plugin;
use Elementor\Controls_Manager;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    /* @return Module */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    static function info()
    {
        return array(
            'title'        => 'Viewer',
            'path'         => __DIR__,
            'classmap'     => [
                'widget' => [

                ],
            ],
        );
    }


    function init_events()
    {
        parent::init_events();


        $this->add_action('wp_footer');

    }

    function _action_wp_footer()
    {
        ?>

        <div class="modal fade" id="sm-elementor-viewer-modal" tabindex="-1" role="dialog" aria-labelledby="sm-elementor-viewer-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sm-elementor-viewer-modal-label">Просмотр</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="height: calc(100vh - 100px);">

                        <div class="viewer" style="height: 100%;">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?
    }


}



