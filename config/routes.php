<?php

use Makraz\EditorjsBundle\Controller\EditorjsUploadController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('editorjs_upload_by_file', '/editorjs/upload/file')
        ->controller([EditorjsUploadController::class, 'uploadByFile'])
        ->methods(['POST'])
    ;

    $routes->add('editorjs_upload_by_url', '/editorjs/upload/url')
        ->controller([EditorjsUploadController::class, 'uploadByUrl'])
        ->methods(['POST'])
    ;
};
