<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.1.15
 * Time: 15.28
 */

namespace Soil\CommentBundle\Controller;


use Soil\CommentBundle\Controller\CORS\CORSTraitForController;
use Soil\CommentBundle\Service\AuthorService;
use Soil\CommentBundle\Service\CommentService;
use Soil\CommentBundle\Service\EntityService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VoteController {
    use CORSTraitForController;

    /**
     * @var CommentService
     */
    protected $commentService;

    /**
     * @var AuthorService
     */
    protected $authorService;

    /**
     * @var EntityService
     */
    protected $entityService;


    public function __construct(CommentService $commentService, EntityService $entityService, AuthorService $authorService) {
        $this->commentService = $commentService;
        $this->entityService = $entityService;
        $this->authorService = $authorService;
    }


    public function voteAction(Request $request)    {
        try {
            if ($request->isMethod('OPTIONS')) {
                return $this->corsController->optionsAction();
            }
            $requestContent = $request->getContent();
            $data = json_decode($requestContent, true);

            $requiredFields = ['user_uri', 'comment_id', 'vote'];
            $missingFields = array_diff($requiredFields, array_keys($data));
            if (count($missingFields) !== 0)  {
                $missingFieldsString = implode(', ', $missingFields);
                throw new \Exception("Fields `$missingFieldsString` is required");
            }

            $commentId = $data['comment_id'];
            $userURI = $data['user_uri'];
            $vote = $data['vote'];

            $user = $this->authorService->getByURI($userURI);

            $comment = $this->commentService->getById($commentId);
            $votes = $comment->getVotes();

            if (!$votes->userCanVote($user)) {
                throw new \Exception("User already vote for this comment");
            }

            $votes->addVote($user, $vote);

            $comment->setVotes($votes);

            $dm = $this->commentService->getDM();
            $dm->persist($votes);
            $dm->flush();


            return $this->answerJSON([
                'success' => true,
                'votePositive' => $votes->getVotesPositive(),
                'voteNegative' => $votes->getVotesNegative()
            ]);
        }
        catch(\Exception $e)    {
            return $this->answerJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    protected function answerJSON($data, $httpCode = 200)    {
        $response = new JsonResponse($data);
        $response->setStatusCode($httpCode);
        $response->headers->add(['Access-Control-Allow-Origin' => '*']);


        return $response;
    }
} 