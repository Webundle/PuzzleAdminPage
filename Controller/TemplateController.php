<?php
namespace Puzzle\Admin\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Puzzle\Admin\PageBundle\Form\Type\TemplateCreateType;
use Puzzle\Admin\PageBundle\Form\Type\TemplateUpdateType;
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
class TemplateController extends Controller
{
    /**
     * @var array $fields
     */
    private $fields;
    
    public function __construct() {
        $this->fields = ['name', 'content'];
    }
    
	/***
	 * List templates
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Security("has_role('ROLE_PAGE') or has_role('ROLE_ADMIN')")
	 */
    public function listAction(Request $request) {
		try {
		    $criteria = [];
		
    		/** @var Puzzle\Admin\AdminBundle\Service\PuzzleAPIClient $apiClient */
    		$apiClient = $this->get('puzzle_connect.api_client');
    		$templates = $apiClient->pull('/page-templates', $criteria);
		
		}catch (BadResponseException $e) {
		    /** @var EventDispatcher $dispatcher */
		    $dispatcher = $this->get('event_dispatcher');
		    $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
		    $templates = [];
		}
		
		return $this->render("PuzzleAdminPageBundle:Template:list.html.twig",[
		    'templates' => $templates
		]);
	}
	
    /***
     * Create a new template
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_PAGE') or has_role('ROLE_ADMIN')")
     */
    public function createAction(Request $request) {
        $data = PuzzleApiObjectManager::hydratate($this->fields, []);
        
        $form = $this->createForm(TemplateCreateType::class, $data, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_page_template_create')
        ]);
        $form->handleRequest($request);
            
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $postData = $form->getData();
            $postData = PuzzleApiObjectManager::sanitize($postData);
            
            try {
                 /** @var Puzzle\Admin\AdminBundle\Service\PuzzleAPIClient $apiClient */
                $apiClient = $this->get('puzzle_connect.api_client');
                $template = $apiClient->push('post', '/page-templates', $postData);
                $this->addFlash('success', $this->get('translator')->trans('message.post', [], 'success'));
                
                return $this->redirectToRoute('admin_page_update', array('id' => $template['id']));
            }catch (BadResponseException $e) {
                $form = ErrorFactory::createFormError($form, $e);
            }
        }
        
        return $this->render("PuzzleAdminPageBundle:Template:create.html.twig", ['form' => $form->createView()]);
    }
    
    /***
     * Show template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_PAGE') or has_role('ROLE_ADMIN')")
     */
    public function showAction(Request $request, $id) {
        /** @var Puzzle\Admin\AdminBundle\Service\PuzzleAPIClient $apiClient */
        $apiClient = $this->get('puzzle_connect.api_client');
        $template = $apiClient->pull('/page-templates/'.$id);
        
        return $this->render("PuzzleAdminPageBundle:Template:show.html.twig", array(
            'template' => $template
        ));
    }
    
    /***
     * Update template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_PAGE') or has_role('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, $id) {
        /** @var Puzzle\Admin\AdminBundle\Service\PuzzleAPIClient $apiClient */
        $apiClient = $this->get('puzzle_connect.api_client');
        $template = $apiClient->pull('/page-templates/'.$id);
        
        $data = ['name'  => $template['name'], 'content' => $template['content']];
        
        $form = $this->createForm(TemplateUpdateType::class, $data, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_page_template_update', ['id' => $id])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $postData = $form->getData();
            $postData = PuzzleApiObjectManager::sanitize($postData);
            
            try {
                /** @var Puzzle\Admin\AdminBundle\Service\PuzzleAPIClient $apiClient */
                $apiClient = $this->get('puzzle_connect.api_client');
                $apiClient->push('put', '/page-templates/'.$template['id'], $postData);
                $this->addFlash('success', $this->get('translator')->trans('message.put', [], 'success'));
                
                return $this->redirectToRoute('admin_page_update', array('id' => $id));
            }catch (BadResponseException $e) {
                $form = ErrorFactory::createFormError($form, $e);
            }
        }
        
        return $this->render("PuzzleAdminPageBundle:Template:update.html.twig", [
            'template' => $template,
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Remove template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_PAGE') or has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id) {
    	try {
    	    /** @var Puzzle\Admin\AdminBundle\Service\PuzzleAPIClient $apiClient */
            $apiClient = $this->get('puzzle_connect.api_client');
            $apiClient->pull('/page-templates/'.$id);
            
        	$response = $apiClient->push('delete', '/page-templates/'.$id);
        	if ($request->isXmlHttpRequest()) {
        	    return new JsonResponse($response);
        	}
        	
        	return $this->redirectToRoute('admin_page_template_list');
    	}catch (BadResponseException $e) {
    	    /** @var EventDispatcher $dispatcher */
    	    $dispatcher = $this->get('event_dispatcher');
    	    $event  = $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
    	    
    	    if ($request->isXmlHttpRequest()) {
    	        return $event->getResponse();
    	    }
    	    
    	    return $this->redirectToRoute('admin_page_template_list');
    	}
    }
}
