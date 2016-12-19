<?php

    namespace Upload\Form;

    use Zend\Form\Form;
    use Zend\InputFilter\InputFilter;

    class UploadForm extends Form
    {
        public function __construct($name = null)
        {
            parent::__construct('Upload');
            $this->setAttribute('method', 'post');
            $this->setAttribute('enctype','multipart/form-data');

            $this->addElements();
            $this->addInputFilter();
        }

        private function addElements()
        {
            $this->add(array(
                'name' => 'file',
                'attributes' => array(
                    'type'  => 'file',
                ),
                'options' => array(
                    'label' => 'File to upload',
                ),
            ));

            $this->add(array(
                'name' => 'password',
                'attributes' => array(
                    'type'  => 'password',
                    'maxlength' => 32
                ),
                'options' => array(
                    'label' => 'Password (optional)',
                ),
            ));

            $this->add(array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Upload now!'
                ),
            ));
        }

        private function addInputFilter()
        {
            $inputFilter = new InputFilter();
            $this->setInputFilter($inputFilter);

            $inputFilter->add([
                'type'     => 'Zend\InputFilter\FileInput',
                'name'     => 'file',
                'required' => true,
                'validators' => [
                    ['name'    => 'FileUploadFile'],
                    ['name' => 'FileSize',
                    'options' => ['max' => 0xA00000] // 10MB
                    ]
                ],
                'filters'  => [
                    [
                        'name' => 'FileRenameUpload',
                        'options' => [
                            'target'=>'./data/upload',
                            'useUploadName'=> false,
                            'useUploadExtension'=> false,
                            'overwrite'=> false,
                            'randomize'=> true
                        ]
                    ]
                ],
            ]);
        }
    }