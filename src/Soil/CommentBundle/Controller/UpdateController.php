<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 29.11.15
 * Time: 2.57
 */

namespace Soil\CommentBundle\Controller;


use Monolog\Logger;
use Soil\CommentBundle\Service\AuthorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateController {

    /**
     * @var AuthorService
     */
    protected $authorService;

    /**
     * @var Logger
     */
    protected $logger;


    public function __construct(AuthorService $authorService) {
        $this->authorService = $authorService;
    }

    public function updateAuthorAction(Request $request)    {
        try {
            $requestContent = $request->getContent();
            $data = json_decode($requestContent, true);
            if (!$data) throw new \Exception('Request malformed');

            $requiredFields = ['author_uri', 'name', 'surname', 'avatar_url'];
            $missingFields = array_diff($requiredFields, array_keys($data));
            if (count($missingFields) !== 0) {
                $missingFieldsString = implode(', ', $missingFields);
                throw new \Exception("Fields `$missingFieldsString` is required");
            }

            $uri = $data['author_uri'];


            $this->logger->addAlert(print_r($data, true));

            $author = $this->authorService->getByURI($uri);

            if ($author)    {
                $author->setName($data['name']);
                $author->setSurname($data['surname']);
                $author->setAvatarURL($data['avatar_url']);

                $this->authorService->getRepository()->getDocumentManager()->flush();

                return new JsonResponse([
                    'success' => true,
                    'updated' => true
                ], 200);

            }
            else    {

                return new JsonResponse([
                    'success' => true,
                    'updated' => false
                ], 200);
            }



        }
        catch (\Exception $e)   {

            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'debug' => (string) $e
            ], 500);
        }

    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }




} 