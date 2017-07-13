<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class TodolistController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function indexAction(EntityManagerInterface $em)
    {
        $repository = $em->getRepository('AppBundle:Todo');
        $todos=$repository->findAll();
        /*echo '<pre>';
        print_r($todos);
        echo '</pre>';
        */
        return $this->render('todolist/list.html.twig',array('todos'=>$todos));
    }

     /**
     * @Route("/todolist/edit/{id}", name="todo_edit")
     */
    public function editTodoAction($id,Request $request)
    {
        $repository =$this->getDoctrine()->getRepository('AppBundle:Todo');
        $todo=$repository->find($id);
        $todo->setName($todo->getName());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription( $todo->getDescription());
        $todo->setPriority( $todo->getPriority());
        $todo->setDueDate( $todo->getDueDate());
        $todo->setDueDate(new \DateTime('today'));

        $form = $this->createFormBuilder($todo)
           
            ->add('name', TextType::class)
             ->add('category', TextType::class)
             ->add('description', TextareaType::class,array('attr' => array('data-mota'=>'mota')))
             ->add('priority', ChoiceType::class,array(
                'choices' => array('Hight' => 'Hight', 'Normal' =>'Normal','Low'=>'Low'),
                'attr' => array('data-mota'=>'mota'),
            
                ))
            ->add('due_date', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Todo'))
            ->getForm();
             $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
        // perform some action...
         
            $name=$form['name']->getData();
            $category=$form['category']->getData();
            $description=$form['description']->getData();
            $priority=$form['priority']->getData();
            $due_date=$form['due_date']->getData();
            $now=new \DateTime('now');
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription( $description);
            $todo->setPriority( $priority);
            $todo->setDueDate( $due_date);
            $todo->setCreateDate( $now);
            $em=$this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            $this->addFlash(
                'notice','Todo added'
            );
             return $this->redirectToRoute('todo_list');
        }
        return $this->render('todolist/edit.html.twig',array('form'=>$form->createView(),'todo'=> $todo));
    }

     /**
     * @Route("/todo/createtodo", name="todo_create")
     */
    public function createTodoAction(Request $request)
    {
        $todo=new Todo();
        $todo->setDueDate(new \DateTime('today'));
        $url = $this->generateUrl('todo_edit',array('id' => '11287'));
         $form = $this->createFormBuilder($todo)
           
            ->add('name', TextType::class,array('attr'=>array('help'=>'Enter Name Of Todo')))
             ->add('category', TextType::class)
             ->add('description', TextareaType::class,array('attr' => array('data-mota'=>'mota')))
             ->add('priority', ChoiceType::class,array(
                'choices' => array('Hight' => 'Hight', 'Normal' =>'Normal','Low'=>'Low'),
                'attr' => array('data-mota'=>'mota'),
            
                ))
            ->add('due_date', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Todo'))
            ->getForm();
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
        // perform some action...
         
            $name=$form['name']->getData();
            $category=$form['category']->getData();
            $description=$form['description']->getData();
            $priority=$form['priority']->getData();
            $due_date=$form['due_date']->getData();
            $now=new \DateTime('now');
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription( $description);
            $todo->setPriority( $priority);
            $todo->setDueDate( $due_date);
            $todo->setCreateDate( $now);
            $em=$this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            $this->addFlash(
                'notice','Todo added'
            );
             return $this->redirectToRoute('todo_list');
        }

        return $this->render('todolist/create.html.twig',array('form'=>$form->createView()));
    }
       /**
     * @Route("/todolist/details/{id}", name="todo_details")
     */
    public function detailsTodoAction($id)
    {
        $repository =$this->getDoctrine()->getRepository('AppBundle:Todo');
        $todo=$repository->find($id);
        return $this->render('todolist/details.html.twig',array('todo'=>$todo));
    }
     /**
     * @Route("/todolist/del/{id}", name="todo_del")
     */
    public function deleteTodoAction($id,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $repository=$em->getRepository('AppBundle:Todo');
        $todo=$repository->find($id);  
        $em->remove($todo);
        $em->flush();
            $this->addFlash(
                'notice','Todo delete'
        );
       return $this->redirectToRoute('todo_list');
    }

}
