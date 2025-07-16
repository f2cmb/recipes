<?php

namespace App\Form;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;

class FormListenerFactory {

    public function autoSlug (string $field): callable
    {

        return function (PreSubmitEvent $event) use ($field) {

            $data = $event->getData();
            
            if (empty($data['slug'])) {
                $slugger = new AsciiSlugger();
                $slug = strtolower($slugger->slug($data[$field]));
                $data['slug'] = $slug;
                $event->setData($data);
            }
        };
    }

    public function timeStamps(): callable
    {
        return function(PostSubmitEvent $event){
            
            $data = $event->getData();

            $data->setUpdatedAt(new \DateTimeImmutable());

            if (!($data->getId())) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }
}