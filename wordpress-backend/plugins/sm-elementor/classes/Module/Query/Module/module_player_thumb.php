<?php

namespace SM_Elementor\Module\Query\Module;


class module_player_thumb extends Common\Base {

    function get_template()
    {
        return <<<EOT

        <article {{container.attrs}}>
        
            <div {{content.attrs}}>
                    
                <div class="row hgutter-5">
                
                    <div class="col-md-4">
                                    
                        <div {{media.attrs}}>
                        
                            {{thumb}}
                             
                            {{media.children}}
                            
                        </div>
                        
                    </div>
                            
                    <div class="col-md-8">
        
                        <div {{info.attrs}}>
                            
                            {{info.children elements="terms,title,excerpt,date,readmore"}}
                            
                        </div>
                            
                    </div>
                
                </div>
              
            </div>
                        
        </article>

EOT;
    }

}