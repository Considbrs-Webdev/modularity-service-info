<?php

namespace ModularityServiceInfo\Decorators;

use ModularityServiceInfo\PostType\ServiceInformation;
use Municipio\PostObject\PostObjectInterface;

class Decorators {
    /**
     * Initialize decorators and setup
     */
    public function __construct() {
        // Decorate the PostObject so getTitle() returns empty, hiding the H1 in templates
        add_filter('Municipio/DecoratePostObject', function (PostObjectInterface $postObject) {
            if ($postObject->getPostType() !== ServiceInformation::POST_TYPE_NAME) {
                return $postObject;
            }

            $postObject = new ServiceMetaDecorator($postObject);
            
            return $postObject;
        }, 20);
    }
}
