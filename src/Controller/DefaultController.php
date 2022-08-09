<?php

namespace App\Controller;

use App\Entity\Exp;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/index', name: 'indice')]
    public function index(Request $request, UserRepository $UsuarioRepository): Response
    {
        return $this->render('default/index.html.twig', [
            'usuarios' => $UsuarioRepository->findAll()
        ]);
    }

    #[Route('/edit/{id}/', name: 'editar')]
    public function edit(Request $request, UserRepository $userRepository, User $user): Response
    {
        $form = $this->createForm(UserType::class,$user);
        //dd($user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $userRepository->add($user,true);

            return $this->redirectToRoute('indice',[],Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('default/edit.html.twig',[
            'usuario' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}/', name: 'eliminar')]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token')))
        {
            $userRepository->remove($user,true);
        }

        return $this->redirectToRoute('indice',[],Response::HTTP_SEE_OTHER);
    }

    #[Route('/mostrar/{id}', name: 'mostrar_usuario', methods: ['GET'])]
    public function show(User $user): Response
    {
        dd($user->getExps()->getValues());
        return $this->render('default/show.html.twig', [
            'usuario' => $user,
        ]);
    }


    #[Route('/newUser', name: 'agregar_nuevo', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $UsuarioRepository, EntityManagerInterface $em): Response
    {
        //$entityManager = $this->getDoctrine()->getManager();
        $user = new User();

        $exp = new Exp();
        $exp->setTitle('tituloSeteado');
        $exp->setDateFrom(new \DateTime());
        $exp->setUsuario($user);
        $exp->setDateTo(new \DateTime());
        $user->addExp($exp);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() )
        {
            $UsuarioRepository->add($user, true);
           // dd($user->getExps()->getValues());
            //dd($user);
            $em->persist($user);
            $em->persist($exp);
            $em->flush();
            return $this->redirectToRoute('indice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('default/new.html.twig', [
            'user' => $user,
            'formulario' => $form,
        ]);
    }
}
