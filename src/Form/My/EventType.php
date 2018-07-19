<?php

namespace App\Form\My;

use App\Constant\LabelConstant;
use App\Entity\My\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(LabelConstant::DESCRIPTION, TextType::class, ['trim' => true])
            ->add(LabelConstant::START_DATE_TIME, DateTimeType::class,
                ['format' => 'yyyy-MM-dd HH:mm:ss', 'widget' => 'single_text'])
            ->add(LabelConstant::END_DATE_TIME, DateTimeType::class,
                ['format' => 'yyyy-MM-dd HH:mm:ss', 'widget' => 'single_text']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Event::class,
                'csrf_protection' => false,
            ]
        );
    }
}