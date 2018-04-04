<?php

namespace App\Controller;

use App\Entity\User;
use App\Interactor\Exception\UserFollowException;
use App\Interactor\User\GetUsers;
use App\Interactor\User\FollowUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;

class UserController extends RestController
{
    private $getUsersInteractor;
    private $followUserInteractor;

    public function __construct(
        GetUsers $getUsersInteractor,
        FollowUser $followUserInteractor
    ) {
        $this->getUsersInteractor = $getUsersInteractor;
        $this->followUserInteractor = $followUserInteractor;
    }

    /**
     * @Operation(
     *     summary="List of all users",
     *     tags={"User"},
     *     @SWG\Parameter(
     *         name="name", in="query", required=false, type="string",
     *         description="User name"
     *     ),
     *     @SWG\Parameter(
     *         name="surname", in="query", required=false, type="string",
     *         description="User surname"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when operation is successful",
     *          @SWG\Schema(@SWG\Property(property="data", @Model(type=App\Entity\User::class)))
     *     )
     * )
     */
    public function listAction(Request $request)
    {
        $name = $request->query->get('name');
        $surname = $request->query->get('surname');

        $users = $this->getUsersInteractor->execute($name, $surname);

        $response = $this->createJsonResponse(
            Response::HTTP_OK,
            $this->serializer->serialize(['data' => $users], 'json')
        );

        return $response;
    }

    /**
     * @Operation(
     *     summary="Follow user action",
     *     tags={"User"},
     *     @SWG\Parameter(
     *         name="id", in="path", required=true, type="integer"
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when operation is successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when operation failed"
     *     ),
     * )
     */
    public function followAction(User $followee)
    {
        $follower = $this->getUser();

        try {
            $this->followUserInteractor->execute($followee, $follower);
        } catch (UserFollowException $exception) {
            $body = $exception->getMessage();

            return $this->createJsonResponse(Response::HTTP_BAD_REQUEST, json_encode(['error' => $body]));
        }

        return $this->createJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
