<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ToDo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AplikacjaController extends Controller {

    /**
     * @Route("/", name="aplikacja")
     */
    public function indexAction(Request $request) {
        $todos = $this->getDoctrine()->getRepository('AppBundle:ToDo')->findAll();

        return $this->render('/aplikacja/index.html.twig', array(
                    'todos' => $todos
        ));
    }

    /**
     * @Route("/create", name="aplikacja_create")
     */
    public function createAction(Request $request) {
        $todo = new ToDo;

        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('priority', ChoiceType::class, array('choices' => array('Niski' => 'Niski', 'Średni' => 'Średni', 'Wysoki' => 'Wysoki'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('due_time', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
                ->add('submit', SubmitType::class, array('label' => 'Stwórz zadanie', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_time = $form['due_time']->getData();

            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueTime($due_time);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash('notice', 'ToDo Added');

            return $this->redirectToRoute('aplikacja');
        }
        return $this->render('/aplikacja/create.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/details/{id}", name="aplikacja_details")
     */
    public function detailsAction($id) {
        $todo = $this->getDoctrine()->getRepository('AppBundle:ToDo')->find($id);

        return $this->render('/aplikacja/details.html.twig', array(
                    'todo' => $todo
        ));
    }

    /**
     * @Route("/edit/{id}", name="aplikacja_edit")
     */
    public function editAction($id, Request $request) {
        $todo = $this->getDoctrine()->getRepository('AppBundle:ToDo')->find($id);

        $now = new\DateTime('now');

        $todo->setName($todo->getName());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription($todo->getDescription());
        $todo->setPriority($todo->getPriority());
        $todo->setDueTime($todo->getDuetime());
        $todo->setCreateDate($now);

        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('priority', ChoiceType::class, array('choices' => array('Niski' => 'Niski', 'Średni' => 'Średni', 'Wysoki' => 'Wysoki'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('due_time', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Edytuj zadanie', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_time = $form['due_time']->getData();

            $now = new\DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:ToDo')->find($id);

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueTime($due_time);
            $todo->setCreateDate($now);

            $em->flush();

            $this->addFlash('notice', 'ToDo Updated');

            return $this->redirectToRoute('aplikacja');
        }

        return $this->render('/aplikacja/edit.html.twig', array(
                    'todo' => $todo,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/delete/{id}", name="aplikacja_delete")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:ToDo')->find($id);

        $em->remove($todo);
        $em->flush();

        $this->addFlash('notice', 'Todo Removed');

        return $this->redirectToRoute('aplikacja');
    }

}
