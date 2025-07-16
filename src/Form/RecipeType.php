<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Recipe;
use App\Enum\RecipeRegime;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => '',
                'attr' => ['rows' => 5]
            ])
            ->add('duration', NumberType::class)
            ->add('regime', EnumType::class, [
                'class' => RecipeRegime::class,
                'choice_label' => fn (RecipeRegime $regime) => $regime->getLabel()
            ])
            ->add('slug', HiddenType::class, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    ]),
                ],
            ])
             ->add('save', SubmitType::Class, [
                'label' => 'Enregistrer'
            ])
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
            'data_class' => Recipe::class
        ]);
    }
}
