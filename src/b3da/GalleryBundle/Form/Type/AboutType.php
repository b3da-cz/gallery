<?php


namespace b3da\GalleryBundle\Form\Type;


use b3da\GalleryBundle\Entity\About;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutType extends AbstractType
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
                'required' => false,
            ])
            ->add('heading', TextType::class, [
                'label' => 'Heading',
                'required' => false,
            ])
            ->add('subheading', TextType::class, [
                'label' => 'Subheading',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'required' => false,
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text',
                'required' => false,
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Is "About Page" active?',
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
            'data_class' => About::class,
        ]);
    }

    public function getBlockPrefix() {
        return 'image';
    }
}
