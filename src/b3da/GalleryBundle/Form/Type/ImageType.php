<?php


namespace b3da\GalleryBundle\Form\Type;


use b3da\GalleryBundle\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', NumberType::class, [
                'label' => 'Position',
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('width', TextType::class, [
                'label' => 'Image width (px)',
                'required' => false,
            ])
            ->add('height', TextType::class, [
                'label' => 'Image height (px)',
                'required' => false,
            ])
            ->add('isSpherical', CheckboxType::class, [
                'label' => 'Is spherical?',
                'required' => false,
            ])
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
            'data_class' => Image::class,
        ]);
    }

    public function getBlockPrefix() {
        return 'image';
    }
}
