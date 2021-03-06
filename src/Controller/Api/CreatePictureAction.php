<?php

namespace App\Controller\Api;

use App\Entity\Advert;
use App\Entity\Picture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\EntityManagerInterface;

final class CreatePictureAction
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(Request $request): Picture
    {
        $uploadedFile = $request->files->get('file');
        $advertId = (int)$request->get('advert');

        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $picture = new Picture();
        $picture->setFile($uploadedFile) ;
        $picture->setCreatedAt(new \DateTimeImmutable());

        /*if($advertId !== null){
            $advert =  $this->manager->getRepository(Advert::class)->find($advertId);
            $picture->setAdvert($advert) ;
        }*/

        return $picture;
    }
}