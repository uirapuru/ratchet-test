<?php
namespace RatchetTest\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'author',
                'entity',
                [
                    'class'       => "RatchetTest\Bundle\CoreBundle\Entity\User",
                    'property'    => 'username',
                    'empty_value' => 'Choose author',
                    'empty_data'  => null,
                    'required'    => true,
                    'label' => 'Author',
                ]
            )
            ->add('title', 'text')
            ->add('body', 'textarea')
            ->add('submit', 'submit')
        ;
    }

    public function getName()
    {
        return 'ratchettest_document';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => 'RatchetTest\Bundle\CoreBundle\Entity\Document',
            'csrf_protection' => false,
        ]);
    }
}
