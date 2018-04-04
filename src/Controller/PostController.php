<?php

namespace App\Controller;

use App\Entity\Post;
use App\Interactor\Post\CreatePost;
use App\Interactor\Post\GetPosts;
use App\Interactor\Post\UpdatePost;
use App\Interactor\Post\DeletePost;
use App\Interactor\Exception\ValidationException;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;

class PostController extends RestController
{
    private $createPostInteractor;
    private $updatePostInteractor;
    private $deletePostInteractor;
    private $getPostsInteractor;

    public function __construct(
        CreatePost $createPostInteractor,
        UpdatePost $updatePostInteractor,
        DeletePost $deletePostInteractor,
        GetPosts $getPostsInteractor
    ) {
        $this->createPostInteractor = $createPostInteractor;
        $this->updatePostInteractor = $updatePostInteractor;
        $this->deletePostInteractor = $deletePostInteractor;
        $this->getPostsInteractor = $getPostsInteractor;
    }

    /**
     * @Operation(
     *     summary="List of all posts",
     *     tags={"Post"},
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when operation is successful",
     *          @SWG\Schema(@SWG\Property(property="data", @Model(type=App\Entity\Post::class)))
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when user is unauthorized."
     *     )
     * )
     */
    public function listAction()
    {
        $posts = $this->getPostsInteractor->execute();

        $response = $this->createJsonResponse(
            Response::HTTP_OK,
            $this->serializer->serialize(['data' => $posts], 'json')
        );

        return $response;
    }

    /**
     * @Operation(
     *     summary="Post create action",
     *     tags={"Post"},
     *     @SWG\Response(
     *         response="201",
     *         description="Returned when operation is successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when post contain incorrect data."
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when user is unauthorized."
     *     )
     * )
     */
    public function createAction(Request $request)
    {
        $content = $request->getContent();

        $context = new DeserializationContext();
        $context->setGroups('create');

        $post = $this->serializer->deserialize($content, Post::class, 'json', $context);

        try {
            $this->createPostInteractor->execute($post, $this->getUser());
        } catch (ValidationException $exception) {
            $body = $exception->getErrors();

            return $this->createJsonResponse(Response::HTTP_BAD_REQUEST, json_encode($body));
        }

        $response = $this->createJsonResponse(
            Response::HTTP_CREATED,
            '',
            ['Location' => sprintf('%s/%d', $request->getPathInfo(), $post->getId())]
        );

        return $response;
    }

    /**
     * @Operation(
     *     summary="Post update action",
     *     tags={"Post"},
     *     @SWG\Parameter(
     *         name="id", in="path", required=true, type="integer"
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when operation is successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when post contain incorrect data."
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when user is unauthorized."
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when user does not have permissions."
     *     )
     * )
     */
    public function updateAction(Post $post, Request $request)
    {
        if ($this->getUser() !== $post->getUser()) {
            return $this->createJsonResponse(Response::HTTP_FORBIDDEN);
        }
        $content = json_decode($request->getContent(), true);
        $content['id'] = $post->getId();

        $context = new DeserializationContext();
        $context->setGroups('update');
        $context->attributes->set('target', $post);

        $post = $this->serializer->deserialize(json_encode($content), Post::class, 'json', $context);
        try {
            $this->updatePostInteractor->execute($post);
        } catch (ValidationException $exception) {
            $body = $exception->getErrors();

            return $this->createJsonResponse(Response::HTTP_BAD_REQUEST, json_encode($body));
        }

        return $this->createJsonResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * @Operation(
     *     summary="Post delete action",
     *     tags={"Post"},
     *     @SWG\Parameter(
     *         name="id", in="path", required=true, type="integer"
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when operation is successful"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when user is unauthorized."
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when user does not have permissions."
     *     )
     * )
     */
    public function deleteAction(Post $post, Request $request)
    {
        if ($this->getUser() !== $post->getUser()) {
            return $this->createJsonResponse(Response::HTTP_FORBIDDEN);
        }
        $content = json_decode($request->getContent(), true);
        $content['id'] = $post->getId();

        $this->deletePostInteractor->execute($post);

        return $this->createJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
