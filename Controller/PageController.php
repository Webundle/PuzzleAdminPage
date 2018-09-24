<?php
namespace Puzzle\Admin\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Puzzle\Admin\PageBundle\Form\Type\PageCreateType;
use Puzzle\Admin\PageBundle\Form\Type\PageUpdateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use GuzzleHttp\Exception\BadResponseException;
use Puzzle\ConnectBundle\ApiEvents;
use Puzzle\ConnectBundle\Event\ApiResponseEvent;
use Puzzle\ConnectBundle\Service\PuzzleApiObjectManager;
use Puzzle\ConnectBundle\Service\ErrorFactory;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class PageController extends Controller
{
    /**
     * @var array $fields
     */
    private $fields;
    
    public function __construct() {
        $this->fields = ['name', 'template', 'content', 'parent'];
    }
    
	/***
	 * List pages
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Security("has_role('ROLE_BLOG') or has_role('ROLE_ADMIN')")
	 */
    public function listAction(Request $request) {
		try {
		    /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
    		$apiClient = $this->get('puzzle_connect.api_client');
    		$pages = $apiClient->pull('/pages');
		}catch (BadResponseException $e) {
		    /** @var EventDispatcher $dispatcher */
		    $dispatcher = $this->get('event_dispatcher');
		    $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
		    $pages = [];
		}
		
		return $this->render("PuzzleAdminPageBundle:Page:list.html.twig",['pages' => $pages]);
	}
	
    /***
     * Create a new page
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_BLOG') or has_role('ROLE_ADMIN')")
     */
    public function createAction(Request $request) {
        $data = PuzzleApiObjectManager::hydratate($this->fields, []);
        $form = $this->createForm(PageCreateType::class, $data, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_page_create')
        ]);
        $form = $this->addFormPart($request, $form, $data);
        $form->handleRequest($request);
            
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            /** @var Puzzle\Admin\MediaBundle\Service\UploadManager $uploader */
            $uploader = $this->get('admin.media.upload_manager');
            $uploads = $uploader->prepareUpload($_FILES, $request->getSchemeAndHttpHost());
            
            $postData = $form->getData();
            $postData['picture'] = $uploads && count($uploads) > 0 ? $uploads[0] : $postData['file-url'] ?? null;
            $postData = PuzzleApiObjectManager::sanitize($postData);
            
            try {
                /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
                $apiClient = $this->get('puzzle_connect.api_client');
                $page = $apiClient->push('post', '/pages', $postData);
                $this->addFlash('success', $this->get('translator')->trans('message.post', [], 'success'));
                
                return $this->redirectToRoute('admin_page_update', array('id' => $page['id']));
            }catch (BadResponseException $e) {
                $form = ErrorFactory::createFormError($form, $e);
            }
        }
        
        return $this->render("PuzzleAdminPageBundle:Page:create.html.twig", ['form' => $form->createView()]);
    }
    
    /***
     * Show page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_BLOG') or has_role('ROLE_ADMIN')")
     */
    public function showAction(Request $request, $id) {
        try {
            /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
            $apiClient = $this->get('puzzle_connect.api_client');
            $page = $apiClient->pull('/pages/'.$id);
        }catch (BadResponseException $e) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
            $page = [];
        }
        
        return $this->render("PuzzleAdminPageBundle:Page:show.html.twig", array(
            'page' => $page
        ));
    }
    
    /***
     * Update page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_BLOG') or has_role('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, $id) {
        /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
        $apiClient = $this->get('puzzle_connect.api_client');
        $page = $apiClient->pull('/pages/'.$id);
        
        $data = [
            'name'  => $page['name'],
            'template' => $page['template'] ?? null,
            'parent' => $page['parent'] ?? null,
            'content' => $page['content'],
        ];
        
        $form = $this->createForm(PageCreateType::class, $data, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_page_update', ['id' => $id])
        ]);
        $form = $this->addFormPart($request, $form, $data);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            /** @var Puzzle\Admin\MediaBundle\Service\UploadManager $uploader */
            $uploader = $this->get('admin.media.upload_manager');
            $uploads = $uploader->prepareUpload($_FILES, $request->getSchemeAndHttpHost());
            
            $postData = $form->getData();
            $postData['picture'] = $uploads && count($uploads) > 0 ? $uploads[0] : $postData['file-url'] ?? null;
            $postData = PuzzleApiObjectManager::sanitize($postData);
            
            try {
                $apiClient->push('put', '/pages/'.$id, $postData);
                return $this->redirectToRoute('admin_page_update', array('id' => $id));
            }catch (BadResponseException $e) {
                $form = ErrorFactory::createFormError($form, $e);
            }
        }
        
        return $this->render("PuzzleAdminPageBundle:Page:update.html.twig", [
            'page' => $page,
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Remove page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_BLOG') or has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id){
        try {
            /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
            $apiClient = $this->get('puzzle_connect.api_client');
            $response = $apiClient->push('delete', '/pages/'.$id);
            
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse($response);
            }
            
            return $this->redirectToRoute('admin_page_list');
        }catch (BadResponseException $e) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $event  = $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
            
            if ($request->isXmlHttpRequest()) {
                return $event->getResponse();
            }
            
            return $this->redirectToRoute('admin_page_list');
        }
    }
    
    
    public function addFormPart($request, $form, $data) {
        /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
        $apiClient = $this->get('puzzle_connect.api_client');
        
        $urls = [
            'template' => '/page-templates',
            'parent' => '/pages'
        ];
        
        foreach ($urls as $key => $url) {
            try {
                $items = $apiClient->pull($url, ['fields' => 'name,id']);
                $array = [];
                if ($items) {
                    foreach ($items as $item) {
                        $array[$item['name']] = $item['id'];
                    }
                }
                
                $form->add($key, ChoiceType::class, array(
                    'translation_domain' => 'admin',
                    'label' => 'cms.page.'.$key,
                    'label_attr' => ['class' => 'form-label'],
                    'choices' => $array,
                    'placeholder' => 'layout.select',
                    'data' => $data[$key],
                    'attr' => ['class' => 'select'],
                ));
            }catch (BadResponseException $e) {
                /** @var EventDispatcher $dispatcher */
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
            }
            
        }
        
        return $form;
    }
}
