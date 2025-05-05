<?php

namespace App\Services;

use App\Models\Friend;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use App\Helper\AuthenticateUser;

class FriendService
{
    public function sendFriendRequest($userOneId, $userTwoId)
    {
        if ($userOneId === $userTwoId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot send a friend request to yourself',
                'status' => 400
            ]);
        }
        $friendRequest = Friend::where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userOneId)
                ->where('user_two_id', $userTwoId);
        })->orWhere(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userTwoId)
                ->where('user_two_id', $userOneId);
        })->first();

        if($friendRequest && $friendRequest->status === 'blocked') {
            return [
                'success' => false,
                'message' => 'You have blocked this user or they have blocked you',
                'status' => 400
            ];
        }

        if ($friendRequest && $friendRequest->status !== 'rejected') {
            return [
                'success' => false,
                'message' => 'Friend request already sent or already friends',
                'status' => 400
            ];
        }

        Friend::updateOrInsert([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId
        ],
        ['status' => 'pending']);

        return [
            'success' => true,
            'message' => 'Friend request sent successfully',
            'status' => 200
        ];
    }

    public function acceptFriendRequest($userOneId, $userTwoId)
    {
        $friendRequest = Friend::where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userTwoId)
                ->where('user_two_id', $userOneId);
        })->first();

        if (!$friendRequest || $friendRequest->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'No pending friend request found',
                'status' => 400
            ];
        }

        $friendRequest->update(['status' => 'accepted']);

        return [
            'success' => true,
            'message' => 'Friend request accepted successfully',
            'status' => 200
        ];
    }

    public function rejectFriendRequest($userOneId, $userTwoId)
    {
        $friendRequest = Friend::where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userOneId)
                ->where('user_two_id', $userTwoId);
        })->orWhere(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userTwoId)
                ->where('user_two_id', $userOneId);
        })->first();

        if (!$friendRequest || $friendRequest->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'No pending friend request found',
                'status' => 400
            ];
        }

        $friendRequest->delete();

        return [
            'success' => true,
            'message' => 'Friend request rejected successfully',
            'status' => 200
        ];
    }

    public function removeFriend($userOneId, $userTwoId)
    {
        if ($userOneId === $userTwoId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot remove yourself as a friend',
                'status' => 400
            ]);
        }

        $friend = Friend::where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userOneId)
                ->where('user_two_id', $userTwoId);
        })->orWhere(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userTwoId)
                ->where('user_two_id', $userOneId);
        })->first();

        if(!$friend) {
            return response()->json([
                'success' => false,
                'message' => 'No friendship found',
                'status' => 400
            ]);
        }

        $friend->delete();

        return response()->json([
            'success' => true,
            'message' => 'Friend removed successfully',
            'status' => 200
        ]);

    }

    public function getFriends($userId)
    {
        $friends = DB::table('friends')
        ->where(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)
                  ->orWhere('user_two_id', $userId);
        })
        ->join('users', function ($join) use ($userId) {
            $join->on('friends.user_one_id', '=', 'users.uuid')
                 ->orOn('friends.user_two_id', '=', 'users.uuid');
        })
        ->select('friends.*', 'users.nickname', 'users.email', 'users.name')
        ->where('users.uuid', '!=', $userId)
        ->get();
    
    return $friends;
    
    }

    public function getFriendRequests($userId)
    {
        $friendRequests = DB::table('friends')
            ->where('user_two_id', $userId)
            ->where('status', 'pending')
            ->get();

        return $friendRequests;
    }
    
    public function blockUser($userId, $blockedUserId)
    {
        if ($userId === $blockedUserId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot block yourself',
                'status' => 400
            ]);
        }

        $block = Friend::where(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_one_id', $userId)
                ->where('user_two_id', $blockedUserId);
        })->orWhere(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_one_id', $blockedUserId)
                ->where('user_two_id', $userId);
        })->where('status', 'blocked')
        ->first();

        if ($block) {
            return response()->json([
                'success' => false,
                'message' => 'User already blocked',
                'status' => 400
            ]);
        }

        $friend = Friend::where(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_one_id', $userId)
                ->where('user_two_id', $blockedUserId);
        })->orWhere(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_one_id', $blockedUserId)
                ->where('user_two_id', $userId);
        })->first();

        if ($friend) {
            $friend->update(['status' => 'blocked']);
        } else {
            Friend::create([
                'user_one_id' => $userId,
                'user_two_id' => $blockedUserId,
                'status' => 'blocked'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully',
            'status' => 200
        ]);
    }
    
    public function unblockUser($userId, $blockedUserId)
    {
        if ($userId === $blockedUserId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot unblock yourself',
                'status' => 400
            ]);
        }

        $block = Friend::where(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_one_id', $userId)
                ->where('user_two_id', $blockedUserId);
        })->orWhere(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_one_id', $blockedUserId)
                ->where('user_two_id', $userId);
        })->where('status', 'blocked')
        ->first();

        if (!$block) {
            return response()->json([
                'success' => false,
                'message' => 'User not blocked',
                'status' => 400
            ]);
        }

        $block->delete();

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully',
            'status' => 200
        ]);
    }

    public function getBlockedUsers($userId)
    {
        $blockedUsers = DB::table('friends')
            ->where(function ($query) use ($userId) {
                $query->where('user_one_id', $userId)
                    ->where('status', 'blocked');
            })->orWhere(function ($query) use ($userId) {
                $query->where('user_two_id', $userId)
                    ->where('status', 'blocked');
            })
            ->get();

        return $blockedUsers;
    }
}