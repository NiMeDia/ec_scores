<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

//use Zend\Mvc\Controller\AbstractActionController;
use Application\Controller\AbstractEntityBasedController;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity as DoctrineHydrator;
//use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class ApiController extends AbstractEntityBasedController
{
    public function indexAction()
    {
        return new \Zend\View\Model\JsonModel([
            'api' => 'EC2020',
            'version' => '1',
            'author' => 'NiMeDia'
        ]);
    }
    public function scoresAction()
    {
        $offset = $this->params()->fromQuery('o', 0);
        $limit = $this->params()->fromQuery('l', 10);
//        $startDate = $this->params()->fromQuery('s', 'none');
//        $startedWithZeroOnly = $this->params()->fromQuery('z', 0);
        $searchUserId = $this->params()->fromQuery('u', '');
        $version = $this->params()->fromQuery('v', '');
        $exclusiveSearch = $this->params()->fromQuery('x', 0);

        $em = $this->getEntityManager();
        $entries = [];
        $wasError = false;
        try {
            $sql = 'SELECT s.year, s.startedWithZero, s.name, (CASE WHEN s.user=:search THEN true ELSE false END) AS isYou '
            . 'FROM (SELECT ss.year, ss.startedWithZero, ss.user, u.displayName AS name FROM scores ss INNER JOIN users u ON ss.user = u.id AND (u.id = ss.user) WHERE ss.version = :version ORDER BY ss.year DESC) s '
            . (($exclusiveSearch > 0) ? 'WHERE isYou = 1' : '')
            . ' GROUP BY s.user ORDER BY s.year DESC LIMIT :limit OFFSET :offset';

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindParam(':search', $searchUserId, \PDO::PARAM_STR);
            $stmt->bindParam(':version', $version, \PDO::PARAM_STR);
            $stmt->bindParam(':limit', intval($limit), \PDO::PARAM_INT);
            $stmt->bindParam(':offset', intval($offset), \PDO::PARAM_INT);
            $stmt->execute();
            $entries = $stmt->fetchAll();
        } catch (\Exception $ex) {
            error_log('Error at scores API:' . $ex->getMessage());
            $wasError = true;
        }
//        $repository = $em->getRepository('Application\Entity\Score');
//        $queryBuilder = $repository->createQueryBuilder('s');
//        $queryBuilder->select('MAX(s.year) as year, s.startedWithZero, u.displayName as name, (CASE WHEN s.user=:search THEN true ELSE false END) AS isYou');
////        $queryBuilder->select('s.year as year, s.startedWithZero, u.displayName as name, (CASE WHEN s.user=:search THEN true ELSE false END) AS isYou');
//        $queryBuilder->setParameter('search', $searchUserId);
//        $queryBuilder->join('s.user', 'u', 'WITH', 'u.id = s.user');
//
//        $queryBuilder->groupBy('s.user');
//        $queryBuilder->orderBy('year', 'DESC');
//        if($startedWithZeroOnly) {
//            $queryBuilder->andWhere("s.startedWithZero = 1");
//        }
//        switch($startDate) {
//            case 'week':
//                $start = new \DateTime();
//                $start->modify('-1 week');
//                $queryBuilder->andWhere("s.publishDate > :starttime");
//                $queryBuilder->setParameter('starttime', $start);
//                break;
//            case 'month':
//                $start = new \DateTime();
//                $start->modify('-1 month');
//                $queryBuilder->andWhere("s.publishDate > :starttime");
//                $queryBuilder->setParameter('starttime', $start);
//                break;
//            case 'none':
//            default :
//                break;
//        }
//        $queryBuilder->setFirstResult($offset);
//        $queryBuilder->setMaxResults($limit);
////        var_dump($queryBuilder->getQuery()->getSql());
//        $entries = $queryBuilder->getQuery()->getArrayResult();

        $result = array(
            'scores' => $entries,
            'limit' => $limit,
            'offset' => $offset,
            'error' => $wasError,
        );

        return new \Zend\View\Model\JsonModel($result);
    }

    public function scoreAction() {
        //uncomment to test plain html form
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(400);
            return new \Zend\View\Model\JsonModel(['errors' => ['Invalid request']]);
        }

        try {
            $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
            $score = new \Application\Entity\Score();
            $builder = new AnnotationBuilder($entityManager);
            $form = $builder->createForm($score);
            $form->setHydrator(new DoctrineHydrator($entityManager, true));
            $form->bind($score);
            if ($this->getRequest()->isPost()) {
                $data = $this->params()->fromPost();
                $form->setData($data);
                if ($form->isValid()) {
                    if(!isset($data['checksum'])) {
                        $this->getResponse()->setStatusCode(400);
                        return new \Zend\View\Model\JsonModel(array('errors' => ['missing checksum']));
                    }

                    $checksum = self::getScoreChecksum($data);
                    if($checksum !== $data['checksum']) {
                        $this->getResponse()->setStatusCode(401);
                        return new \Zend\View\Model\JsonModel(array('errors' => ['checksum missmatch']));
                    }

                    //@FIXME: change to simple text field 
                    //@TODO: why is this not possible via doctrine cascade?
                    if(isset($data['user']['id'])) {
                        $userRepo = $entityManager->getRepository(\Application\Entity\User::class);
                        $user = $userRepo->find($data['user']['id']);
                        if($user) {
                            //user update:
//                            $hydrator = new \Zend\Hydrator\ClassMethods();
//                            $formUser = $score->getUser();
//                            $userData = $hydrator->extract($formUser);
//                            $hydrator->hydrate($userData, $user);
                            $score->setUser($user);
                        }
                        $formUser = $score->getUser();
                        if(empty($formUser->getDisplayName())) {
                            $formUser->setDisplayName('Anonymous Commander');
                        }
                        $score->setUser($formUser);
                    }
                    $score->setPublishDate(new \DateTime());
                    $entityManager->persist($score);
                    $entityManager->flush();
                    $this->getResponse()->setStatusCode(201);
                } else {
                    $this->getResponse()->setStatusCode(400);
                }
//                return new \Zend\View\Model\JsonModel(array('errors' => $form->getMessages()));
                return new \Zend\View\Model\JsonModel(array('errors' => '100101'));
            }
        } catch(\Exception $e) {
            $this->getResponse()->setStatusCode(500);
//            return new \Zend\View\Model\JsonModel(array('errors' => $e->getMessage()));
            return new \Zend\View\Model\JsonModel(array('errors' => '100100'));
        }

        //human form only for testing purposes:
        $form->add(array(
            'name' => 'checksum',
            'attributes' => array(
                'type' => 'text'
            )
        ));
        
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Save'
            )
        ));
        $view =  new ViewModel();
        $view->setVariable('form',$form);
        return $view;
    }
    
    public static function getScoreChecksum($postData) {
        $value = 'Rocks';
        $value .= $postData['year'];
        $value .= (isset($postData['startedWithZero'])) ? $postData['startedWithZero'] : '';
        $value .= $postData['user']['id'];
        $value .= $postData['version'];
//        $value .= $postData['user']['displayName'];
        return md5('NiMeDia' . md5($value));
    }
    public static function getUserChecksum($postData) {
        $value = 'Rocks';
        $value .= $postData['id'];
        $value .= $postData['displayName'];
//        $value .= $postData['user']['displayName'];
        return md5('NiMeDia' . md5($value));
    }
    
    public function userAction() {
        //uncomment to test plain html form
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(400);
            return new \Zend\View\Model\JsonModel(['errors' => ['Invalid request']]);
        }

        try {
            $data = $this->params()->fromPost();
            $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
            if(! isset($data['id'])) {
                $this->getResponse()->setStatusCode(400);
                return new \Zend\View\Model\JsonModel(['errors' => ['Invalid request']]);
            }
            $userRepo = $entityManager->getRepository(\Application\Entity\User::class);
            $user = $userRepo->find($data['id']);
            if(!$user) {
                $this->getResponse()->setStatusCode(400);
                return new \Zend\View\Model\JsonModel(['errors' => ['Invalid request']]);
            }

            $builder = new AnnotationBuilder($entityManager);
            $form = $builder->createForm($user);
            $form->setHydrator(new DoctrineHydrator($entityManager, true));
            $form->bind($user);
            if ($this->getRequest()->isPost()) {
                $form->setData($data);
                if ($form->isValid()) {
                    if(!isset($data['checksum'])) {
                        $this->getResponse()->setStatusCode(400);
                        return new \Zend\View\Model\JsonModel(array('errors' => ['missing checksum']));
                    }

                    $checksum = self::getUserChecksum($data);
                    if($checksum !== $data['checksum']) {
                        $this->getResponse()->setStatusCode(401);
                        return new \Zend\View\Model\JsonModel(array('errors' => ['checksum missmatch']));
                    }
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->getResponse()->setStatusCode(202);
                } else {
                    $this->getResponse()->setStatusCode(400);
                }
//                return new \Zend\View\Model\JsonModel(array('errors' => $form->getMessages()));
                return new \Zend\View\Model\JsonModel(array('errors' => '100101'));
            }
        } catch(\Exception $e) {
            $this->getResponse()->setStatusCode(500);
//            return new \Zend\View\Model\JsonModel(array('errors' => $e->getMessage()));
            return new \Zend\View\Model\JsonModel(array('errors' => '100100'));
        }

        //human form only for testing purposes:
        $form->add(array(
            'name' => 'checksum',
            'attributes' => array(
                'type' => 'text'
            )
        ));
        
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Save'
            )
        ));
        $view =  new ViewModel();
        $view->setVariable('form',$form);
        return $view;
    }
}
