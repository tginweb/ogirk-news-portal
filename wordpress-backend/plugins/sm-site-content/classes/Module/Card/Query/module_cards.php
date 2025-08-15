<?php


namespace SM_Site_Content\Module\Card\Query;


class module_cards extends \SM_Elementor\Module\Query\Module\Common\Base {

    function get_template()
    {

        return <<<EOT

        <article {{container.attrs}}>
        
            <div {{content.attrs}}>
                    
              
                {{info.children elements="terms,title,econtent,excerpt,date,readmore"}}
                            
              
            </div>
            
            
        </article>

EOT;
    }


}