<?php


    namespace Upload\Controller;

    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    use Zend\View\Model\JsonModel;

    use Upload\Form\UploadForm;
    use Upload\Entity\File;

    class UploadController extends AbstractActionController
    {
        protected $entityManager;
        protected $fileDir = './data/upload/';

        public function __construct($entityManager)
        {
            $this->entityManager = $entityManager;
        }

        public function listAction()
        {
            $files = [];
            $authorized = false;

            $request = $this->getRequest();

            if ($request->isPost())
            {
                $user = $request->getPost('user', null);
                $password = $request->getPost('password', null);

                if ($user == 'admin' and $password == 'password')
                {
                    $authorized = true;
                    $files = $this->entityManager->getRepository(File::class)->findAll();
                }
            }

            return new ViewModel(['files' => $files, 'authorized' => $authorized]);
        }

        public function uploadAction()
        {
            $form = new UploadForm();

            $request = $this->getRequest();

            if ($request->isPost())
            {
                $formData = array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());

                $form->setData($formData);

                if($form->isValid())
                {
                    $data = $form->getData();

                    $file = new File();
                    $file->file = basename($data['file']['tmp_name']);
                    $file->name = $data['file']['name'];
                    $file->size = $data['file']['size'];
                    $file->type = $data['file']['type'];
                    $file->password = $data['password'];

                    $this->entityManager->persist($file);

                    $this->entityManager->flush();

                    return $this->redirect()->toRoute('upload', ['action'=>'success'], ['query'=>['id' => $file->id]]);
                }
                else
                {
                    return $this->redirect()->toRoute('upload', ['action' => 'error']);
                }
            }

            return new ViewModel(['form' => $form]);
        }

        public function getAction()
        {
            $fileId = $this->params()->fromQuery('id');

            if ($file = $this->entityManager->find(File::class, $fileId))
            {
                $password = $this->getRequest()->getPost('password', null);

                if ($file->password)
                {
                    if ($password === null)
                    {
                        return $this->redirect()->toRoute('upload', ['action'=>'protected'], ['query'=>['id' => $file->id]]);
                    }
                    else
                    {
                        if ($file->password !== $password)
                            return $this->redirect()->toRoute('upload', ['action' => 'denied']);
                        else
                            $this->giveFile($file);
                    }
                }
                else
                {
                    $this->giveFile($file);
                }
            }
            else
                $this->notFoundAction();


            return $this->getResponse();
        }


        public function protectedAction()
        {
            if (!$fileId = $this->params()->fromQuery('id'))
                return $this->redirect()->toRoute('upload');
            else
                return new ViewModel(['fileId' => $fileId]);
        }

        public function deleteAction()
        {
            $result = ['success' => false];

            $fileId = $this->params()->fromQuery('id');

            if ($file = $this->entityManager->find(File::class, $fileId))
            {
                $this->entityManager->remove($file);
                $this->entityManager->flush();

                $result['success'] = true;
            }

            return new JsonModel($result);
        }


        public  function fileAction()
        {
            if ($fileId = $this->params()->fromQuery('id'))
            {
                if ($file = $this->entityManager->find(File::class, $fileId))
                {
                    if ($file->password !== '')
                    {
                        $password = $this->getRequest()->getPost('password', null);

                        if ($password === null)
                        {
                            return $this->redirect()->toRoute('upload', ['action'=>'protected'], ['query'=>['id' => $file->id]]);
                        }
                        else
                        {
                            if ($file->password !== $password)
                                return $this->redirect()->toRoute('upload', ['action' => 'denied']);
                            else
                                return new ViewModel(['fileId' => $fileId, 'password' => $password]);
                        }

                        return $this->redirect()->toRoute('upload', ['action'=>'protected'], ['query'=>['id' => $file->id]]);
                    }
                    else
                    {
                        return new ViewModel(['fileId' => $fileId, 'password' => null]);
                    }
                }
            }

            return $this->notFoundAction();
        }

        public function errorAction()
        {

        }

        public function deniedAction()
        {

        }

        public function successAction()
        {
            if ($fileId = $this->params()->fromQuery('id'))
                return new ViewModel(['fileId' => $fileId]);
            else
                return $this->redirect()->toRoute('upload');
        }

        protected function giveFile(File $file)
        {
            $response = $this->getResponse();
            $headers = $response->getHeaders();
            $headers->addHeaderLine("Content-type: " . $file->type);
            $headers->addHeaderLine("Content-length: " . $file->size);
            $headers->addHeaderLine("Content-disposition: attachment; filename=\"" . $file->name .'"');

            $fileContents = file_get_contents($this->fileDir . $file->file);

            if($fileContents !== false)
            {
                $response->setContent($fileContents);
            }
            else
            {
                $this->notFoundAction();
            }
        }
    }