<?php


namespace b3da\GalleryBundle\Form\Type;


use b3da\GalleryBundle\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
//                'attr' => ['class' => 'pure-form pure-form-stacked'],
            ])
            ->add('position', NumberType::class, [
                'label' => 'Position',
                'required' => false,
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Is Public?',
                'required' => false,
            ])
//            ->add('frontImage', null, [
//                'label' => 'Front Image',
//                'required' => false,
//            ])
//            ->add('images', null, [
//                'label' => 'Images',
//                'required' => false,
//            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'pure-button pure-button-primary'],
            ])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }

    public function getBlockPrefix() {
        return 'gallery';
    }
}
