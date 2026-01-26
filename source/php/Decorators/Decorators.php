<?php

namespace ModularityServiceInfo\Decorators;

use ModularityServiceInfo\PostType\ServiceInformation;
use Municipio\PostObject\PostObjectInterface;

class Decorators {
    /**
     * Initialize decorators and setup
     */
    public function __construct() {
        add_filter('Municipio/DecoratePostObject', function (PostObjectInterface $postObject) {
            if ($postObject->getPostType() !== ServiceInformation::POST_TYPE_NAME) {
                return $postObject;
            }

            $postObject = new ServiceMetaDecorator($postObject);
            
            return $postObject;
        }, 20);
    }
}
