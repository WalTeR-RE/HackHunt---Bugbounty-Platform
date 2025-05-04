<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Services\FriendService;
use App\Helper\AuthenticateUser;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    protected $friendService;
    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    public function sendFriendRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|exists:users,nickname',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userOneId = AuthenticateUser::authenticatedUser($request)->uuid;
        $userTwoId = AuthenticateUser::getUserByNickname($request->input('nickname'))->uuid;
        

        $data = $this->friendService->sendFriendRequest($userOneId, $userTwoId);

        return response()->json($data);
    }

    public function acceptFriendRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|exists:users,nickname',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userOneId = AuthenticateUser::authenticatedUser($request)->uuid;
        $userTwoId = AuthenticateUser::getUserByNickname($request->input('nickname'))->uuid;

        $data = $this->friendService->acceptFriendRequest($userOneId, $userTwoId);

        return response()->json($data);
    }
    public function rejectFriendRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|exists:users,nickname',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userOneId = AuthenticateUser::authenticatedUser($request)->uuid;
        $userTwoId = AuthenticateUser::getUserByNickname($request->input('nickname'))->uuid;

        $data = $this->friendService->rejectFriendRequest($userOneId, $userTwoId);

        return response()->json($data);
    }
    public function removeFriend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|exists:users,nickname',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userOneId = AuthenticateUser::authenticatedUser($request)->uuid;
        $userTwoId = AuthenticateUser::getUserByNickname($request->input('nickname'))->uuid;
        
        $data = $this->friendService->removeFriend($userOneId, $userTwoId);

        return response()->json($data);
    }
    public function getFriends(Request $request)
    {
        $userId = AuthenticateUser::authenticatedUser($request)->uuid;

        $friends = $this->friendService->getFriends($userId);

        return response()->json(['friends' => $friends]);
    }
    public function getFriendRequests(Request $request)
    {
        $userId = AuthenticateUser::authenticatedUser($request)->uuid;

        $friendRequests = $this->friendService->getFriendRequests($userId);

        return response()->json(['friend_requests' => $friendRequests]);
    }
    
    public function blockUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|exists:users,nickname',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userOneId = AuthenticateUser::authenticatedUser($request)->uuid;
        $userTwoId = AuthenticateUser::getUserByNickname($request->input('nickname'))->uuid;

        $data = $this->friendService->blockUser($userOneId, $userTwoId);

        return response()->json($data);
    }

    public function unblockUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|exists:users,nickname',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userOneId = AuthenticateUser::authenticatedUser($request)->uuid;
        $userTwoId = AuthenticateUser::getUserByNickname($request->input('nickname'))->uuid;

        $data = $this->friendService->unblockUser($userOneId, $userTwoId);

        return response()->json($data);
    }
    public function getBlockedUsers(Request $request)
    {
        $userId = AuthenticateUser::authenticatedUser($request)->uuid;

        $blockedUsers = $this->friendService->getBlockedUsers($userId);

        return response()->json(['blocked_users' => $blockedUsers]);
    }


}
