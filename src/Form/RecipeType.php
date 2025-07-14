<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Entity\Recipe;
use App\Enum\RecipeRegime;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class, [
                'attr' => ['rows' => 5],
            ])
            ->add('duration', NumberType::class)
            ->add('regime', EnumType::class, [
                'class' => RecipeRegime::class,
                'choice_label' => fn (RecipeRegime $regime) => $regime->getLabel(),
            ])
            ->add('slug', HiddenType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))

        ;
    }

    public function autoSlug(FormEvent $event): void
    {
        $data = $event->getData();
        if (isset($data['title'])) {
            $slugger = new AsciiSlugger();
            $slug = strtolower($slugger->slug($data['title']));
            $data['slug'] = $slug;
            $event->setData($data);
        }
    }

    public function attachTimestamps(FormEvent $event): void
    {
        $data = $event->getData();
        if (!($data instanceof Recipe)) {
           return;
        }

        $data->setUpdatedAt(new \DateTimeImmutable());
        if (!($data->getId())) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
