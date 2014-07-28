<?php

namespace CAII\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CAII\PublicacionesBundle\Entity\Publicacion;
use CAII\PublicacionesBundle\Form\PublicacionType;

/**
 * Publicacion controller.
 *
 * @Route("/publicacion")
 */
class PublicacionController extends Controller
{

    /**
     * Lists all Publicacion entities.
     *
     * @Route("/", name="Publicacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dql = $em->createQueryBuilder();
        
        //Query para tipos de publicacion 
        $dql->select('TipoPublicacion.nombre','TipoPublicacion.id')
            ->from('PublicacionesBundle:Publicacion', 'Publicacion')
            ->Join('Publicacion.TipoPublicacion', 'TipoPublicacion')
            ->orderBy('TipoPublicacion.prioridad', 'ASC')
            ->groupBy('Publicacion.TipoPublicacion');
        $tipos =$dql->getQuery()->getResult();

        //Nombre de los publicaciones
        $publicaciones = $em->getRepository('PublicacionesBundle:TipoPublicacion')->findAll();

        //Nombre de los publicaciones
        $publicacionesid = $em->getRepository('PublicacionesBundle:Publicacion')->findAll();

        //Query para las publicaciones ordenadas por fecha
        $repository = $this->getDoctrine()
                    ->getRepository('PublicacionesBundle:Publicacion');
        $dql = $repository->createQueryBuilder('p')
                ->select('p')
                ->orderBy('p.fecha', 'DESC');
        $entities =$dql->getQuery()->getResult();
                

        //Query miembros-publicacion
        $dql->select('MiembroPublicacion.id i', 
                     'Miembro.nombre nombreMiembro, Miembro.apellidoP', 
                     'Publicacion.id idpublicacion')
            ->from('MiembroBundle:MiembroPublicacion', 'MiembroPublicacion')
            ->Join('MiembroPublicacion.idMiembro', 'Miembro')
            ->Join('MiembroPublicacion.idPublicacion', 'Publicacion')
            ->groupBy('MiembroPublicacion.id')
            ->orderBy('MiembroPublicacion.id');

        $miembros=$dql->getQuery()->getResult();
       

        return array(
            'entities' => $entities,
            'tipos' => $tipos,
            'miembros'=>$miembros,
            'publicaciones'=>$publicaciones,
            'publicacionesid'=>$publicacionesid,
        );
    }

    /**
     * Lists all Miembro entities.
     *
     * @Route("/", name="Publicacion_backend")
     * @Method("GET")
     * @Template("PublicacionesBundle:Publicacion:indexBackend.html.twig")
     */
    public function indexBackendAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicacionesBundle:Publicacion')->findAll();
        return array(
            'entities' => $entities,
                          
            
        );
    }



    /**
     * Creates a new Publicacion entity.
     *
     * @Route("/", name="Publicacion_create")
     * @Method("POST")
     * @Template("PublicacionesBundle:Publicacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Publicacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('Publicacion_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Publicacion entity.
    *
    * @param Publicacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Publicacion $entity)
    {
        $form = $this->createForm(new PublicacionType(), $entity, array(
            'action' => $this->generateUrl('Publicacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Publicacion entity.
     *
     * @Route("/new", name="Publicacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Publicacion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Publicacion entity.
     *
     * @Route("/{id}", name="Publicacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Publicacion entity.
     *
     * @Route("/{id}/edit", name="Publicacion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Publicacion entity.
    *
    * @param Publicacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Publicacion $entity)
    {
        $form = $this->createForm(new PublicacionType(), $entity, array(
            'action' => $this->generateUrl('Publicacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Publicacion entity.
     *
     * @Route("/{id}", name="Publicacion_update")
     * @Method("PUT")
     * @Template("PublicacionesBundle:Publicacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('Publicacion_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Publicacion entity.
     *
     * @Route("/{id}", name="Publicacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Publicacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('publicacion'));
    }

    /**
     * Creates a form to delete a Publicacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('Publicacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
