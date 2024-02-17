<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\Club;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/*#[Route('/student')] *///make sure the others
class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }
    #[Route('/fetch', name: 'app_student_fetch')]
    public function fetch(StudentRepository $repo):Response{
        $result = $repo->findAll();
        return $this->render('student/test.html.twig', [
            'response' => $result,
        ]);
    }

    #[Route('/add', name: 'app_student_add')]
    public function add(ManagerRegistry $mr):Response{
        $s = new Student();
        $s->setName('oussna');
        $s->setLastName('saoudi');
        $s->setEmail('oussna@gmail.com');
        $s->setPhone(2591997);

        $em = $mr->getManager();
        $em->persist($s);
        $em->flush();

        $s1 = new Student();
        $s1->setName('mannoo');
        $s1->setLastName('mzoughi');
        $s1->setEmail('mannoo@gmail.com');
        $s1->setPhone(56889626);

        $em->persist($s1);
        $em->flush();

        $c= new Classroom();
        $c->setName('class1');
        $c->setCreatedAt(new \DateTimeImmutable());

        $em->persist($c);
        $em->flush();

        $s->setClassroom($c);
        $s1->setClassroom($c);
        $em->persist($s);
        $em->persist($s1);
        $em->flush();

        $club = new Club();
        $club->setName('club1');
        $club->setDescription('club1 description');
        $club->setCreatedAt(new \DateTimeImmutable());

        $em->persist($club);
        $em->flush();

        $s->addClub($club);
        $em->persist($s);
        $em->flush();




        return new Response('added');
    }

    #[Route('/remove/{id}', name: 'app_student_rmv')]
    public function remove(StudentRepository $repo,ManagerRegistry $mr,string $id):Response{
        $result = $repo->find($id);

        $em = $mr->getManager();
        $em->remove($result);
        $em->flush();

        return new Response('removed');
    }

    #[Route('/club/register', name: 'app_student_register')]
    public function register():Response{
        return $this->render('student/form.html.twig');
    }

    #[Route('/addF', name: 'app_student_addform')]
    public function addForm(ManagerRegistry $mr,Request $request):Response{

        $s = new Student();
        $form = $this->createForm(StudentType::class,$s);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $mr->getManager();
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute('app_student_fetch');
        }

        return $this->render('student/form.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dql', name: 'app_student_dql')]
    public function dql(EntityManagerInterface $em):Response{
        $req=$em->createQuery('SELECT s FROM App\Entity\Student s WHERE s.name = :name');
        $req->setParameter('name','oussna');
        $result = $req->getResult();
        dd($result);
    }

    #[Route('/dqlRepo', name: 'app_student_dqlRepo')]
    public function dqlRepo(StudentRepository $repo):Response{
        $result = $repo->fetchStudentByName('oussna');
        dd($result);
    }

    #[Route('/dqlcount', name: 'app_student_dqlcount')]
    public function dqlcount(EntityManagerInterface $em):Response{
        $req=$em->createQuery('SELECT COUNT(s) FROM App\Entity\Student s');
        $result = $req->getResult();
        dd($result);
    }

    #[Route('/dqljoin', name: 'app_student_dqljoin')]
    public function dqljoin(EntityManagerInterface $em):Response{
        $req=$em->createQuery('SELECT s.name t,c.name FROM App\Entity\Student s JOIN s.classroom c');
        $result = $req->getResult();
        dd($result);
    }

    #[Route('/dqljoindesc', name: 'app_student_dqljoindec')]
    public function dqljoindesc(EntityManagerInterface $em):Response{
        $req=$em->createQuery('SELECT s.name t,c.name FROM App\Entity\Student s JOIN s.classroom c WHERE s.name = :name ORDER BY s.name DESC');
        $req->setParameter('name','oussna');
        $result = $req->getResult();
        dd($result);
    }
   //query building
    #[Route('/qb', name: 'app_student_qb')]
    public function qb(EntityManagerInterface $em):Response{
        $qb=$em->createQueryBuilder();
        $qb->select('s.name')
            ->from(Student::class,'s')
            ->where('s.name = :name')
            ->setParameter('name','oussna');
        $result = $qb->getQuery()->getResult();
        dd($result);
    }

    #[Route('/qbjoin', name: 'app_student_qbjoin')]
    public function qbjoin(EntityManagerInterface $em):Response{
        $qb=$em->createQueryBuilder();
        $qb->select('s.name t,c.name')
            ->where('s.name = :name')
            ->setParameter('name','oussna')
            ->from(Student::class,'s')
            ->join('s.classroom','c');
        $result = $qb->getQuery()->getResult();
        dd($result);
    }

    #[Route('/qbjoindesc', name: 'app_student_qbjoindesc')]
    public function qbjoindesc(EntityManagerInterface $em):Response
    {
        $qb = $em->createQueryBuilder();
        $qb->select('s.name t,c.name')
            ->from(Student::class, 's')
            ->join('s.classroom', 'c')
            ->where('s.name = :name')
            ->orderBy('s.name', 'DESC')
            ->setParameter('name', 'oussna');
        $result = $qb->getQuery()->getResult();
        dd($result);
    }


}
