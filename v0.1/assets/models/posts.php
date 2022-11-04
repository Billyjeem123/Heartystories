<?php

/**
 *
 */
class Posts extends db
{

  public  function createPost($userid,  $posts)
  {

    $time = time();
    $Utlity =  new Utility();

    $escapeData = $this->escapeData($posts);;
    $postToken = $Utlity->generateAlphaNumericOTP(9);

    $sql = " INSERT INTO article(userid, article,  postToken, time)
        VALUES(:userid, :article, :postToken,  :time)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':userid', $userid);
    $stmt->bindParam(':article', $escapeData);
    $stmt->bindParam(':postToken', $postToken);
    $stmt->bindParam(':time', $time);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {

      $this->outputData(true, 'Post created', null);
      return true;
    }
  }

  public function fetchPostsMessage($to_id)
  {

    $dataArray = array();
    $sql = " SELECT *  FROM message WHERE to_id = '{$to_id}' ";
    $sql .=  " ORDER BY id DESC ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {

      $count = $stmt->rowCount();
      if ($count > 0) {
        $users = new Users();
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($properties as $crown) {
          $username =   $users->FetchIdByName($crown['from_id']);
          $array = [
            'postToken' => ($crown['postToken']),
            "from_id" => $username['username'],
            "article" => $this->validate($crown['article']),
            "time" => date("D d M, Y: H", $crown['time'])
          ];

          array_push($dataArray, $array);
        }
        return $dataArray;
      } else {
        $this->outputData(false,  'No Message available',  null);
        return false;
      }
    }
  }

  public  function createPost2($from_id,  $to_id, $posts)
  {

    $time = time();
    $Utlity =  new Utility();

    $escapeData = $this->escapeData($posts);;
    $postToken = $Utlity->generateAlphaNumericOTP(10);

    $sql = " INSERT INTO message(from_id, to_id,  article, time, postToken)
        VALUES(:from_id, :to_id, :article, :time,  :postToken)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':from_id', $from_id);
    $stmt->bindParam(':to_id', $to_id);
    $stmt->bindParam(':article', $escapeData);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':postToken', $postToken);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {

      $this->outputData(true, 'Sent', null);
      return true;
    }
  }

  public  function validate($str)
  {
    // 
    // $res = str_replace( array( '\'', ' ',
    // ',' , ';', '<',  '\'' , '>', '\'', '', "'"), ' ', $str);
    $res = str_replace('\'', "'",  $str);

    return $res;
  }

  public function fetchComment($postToken, $userid)
  {

    $dataArray = array();
    $sql = " SELECT  *  FROM comment ";
    $sql .= " WHERE  postToken = '$postToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {

      $count = $stmt->rowCount();
      if ($count > 0) {
        $users = new Users();
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($properties as $crown) {
          $username =   $users->FetchIdByName($crown['userid']);
          $verifyCmtLikes = $this->verifyCommentLikes($userid, $crown['id']);
          $countReply = $this->countReply($crown['commentToken']);
          $array = [

            'postToken' => ($crown['postToken']),
            "commentToken" => $crown['commentToken'],
            'userid' => ($crown['userid']),
            "username" => $username['username'],
            "comment" => $this->validate($crown['comment']),
            "likes" => ($crown['likes']),
            "verifylike" => $verifyCmtLikes,
            "total-reply" => $countReply
          ];

          array_push($dataArray, $array);
        }
        return $dataArray;
      } else {
        $this->outputData(false,  'No comment available',  null);
        exit;
      }
    }
  }

  public function fetchReply($commentToken, $userid)
  {

    $dataArray = array();
    $sql = " SELECT  *  FROM tblreply ";
    $sql .= " WHERE  commentToken = '$commentToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {

      $count = $stmt->rowCount();
      if ($count > 0) {
        $users = new Users();
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($properties as $crown) {
          $username =   $users->FetchIdByName($crown['userid']);
          $verifyReplyLikes = $this->verifyReplyLkes($userid, $crown['replyToken']);
          $array = [

            "replyToken" => $crown['replyToken'],
            "commentToken" => $crown['commentToken'],
            'userid' => ($crown['userid']),
            "username" => $username['username'],
            "reply" => $this->validate($crown['reply']),
            "likes" => ($crown['likes']),
            "verifylike" => $verifyReplyLikes,
            "totalReplies" => $count
          ];

          array_push($dataArray, $array);
        }
        return $dataArray;
      } else {
        $this->outputData(false,  'No replies available',  null);
        exit;
        return false;
      }
    }
  }

  public  function createComment($postToken,  $comment, $userid)
  {

    $time = time();
    $Utlity =  new Utility();

    $escapeData = $this->escapeData($comment);;
    $commentToken = $Utlity->generateAlphaNumericOTP(9);

    $sql = " INSERT INTO comment(postToken, comment, userid, commentToken)
      VALUES(:postToken, :comment, :userid,  :commentToken)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':postToken', $postToken);
    $stmt->bindParam(':comment', $escapeData);
    $stmt->bindParam(':userid', $userid);
    $stmt->bindParam(':commentToken', $commentToken);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Something went wrong, please try again..', null);
      return false;
    } else {

      $this->outputData(true, 'comment sent', null);
      return true;
    }
  }

  public function countComment($postToken)
  {
    global $mysqli;

    $sql = " SELECT  postToken  FROM comment WHERE postToken = '{$postToken}' ";
    $result_set = mysqli_query($mysqli, $sql);
    $rows  = mysqli_num_rows($result_set);
    return $rows;
  }

  public function deleteComment($commentToken, $userid)
  {

    $sql = " DELETE FROM comment WHERE  commentToken = '$commentToken' ";
    $sql .= " AND userid = '$userid' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {
      $this->deleteReply($commentToken);
      $this->outputData(true,  'Deleted', null);
      return true;
    }
  }

  public function deleleArticle($postToken, $userid, $type)
  {
    if($type == "Article"){

    $sql = " DELETE FROM article WHERE  postToken = '$postToken' ";
    $sql .= " AND userid = '$userid' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {
      $this->deleteCmtArticle($postToken);
      $this->deleteReplyArticle($postToken);
      $this->outputData(true,  'Deleted', null);
      return true;
      exit;
    }

      }else{

        $sql = " DELETE FROM message WHERE  postToken = '$postToken' ";
    $sql .= " AND from_id = '$userid' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {
      $this->deleteCmtArticle($postToken);
      $this->deleteReplyArticle($postToken);
      $this->outputData(true,  'Deleted', null);
      return true;
      exit;

        
      }
  }
}

  public function deleteCmtArticle($postToken)
  {

    $sql = " DELETE FROM comment  WHERE  postToken = '$postToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {
      return true;
    }
  }

  public function deleteReplyArticle($postToken)
  {

    $sql = " DELETE FROM tblreply WHERE  postToken = '$postToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {
      return true;
    }
  }

  public function deleteReply($commentToken)
  {

    $sql = " DELETE FROM tblreply WHERE  commentToken = '$commentToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {
      $this->outputData(true,  'Deleted', null);
      return true;
    }
  }

  public function fetchPostByToken($postToken)
  {

    $dataArray = array();
    $sql = " SELECT * FROM article WHERE postToken =  '$postToken'  ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $this->outputData(false, 'Unable to process', null);
      return false;
    } else {

      $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($properties as $crown) {
        $user = new Users();
        $username =   $user->FetchIdByName($crown['userid']);
        $countComment =   $this->countComment($crown['postToken']);
        $array = [

          "postToken" => $crown['postToken'],
          'userid' => ($crown['userid']),
          "username" => $username['username'],
          "article" => $this->validate($crown['article']),
          "likes" => ($crown['likes']),
          "total-comment" => $countComment
        ];

        array_push($dataArray, $array);
      }
      return $dataArray;
    }
  }

  public function fetchPostByUser($userid)
  {

    $dataArray = array();
    $sql = " SELECT * FROM article WHERE userid =  '$userid'  ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $this->outputData(false, 'Unable to process', null);
      return false;
    } else {

      $count = $stmt->rowCount();

      if ($count > 0) {

        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($properties as $crown) {
          $user = new Users();
          $username =   $user->FetchIdByName($crown['userid']);
          $countComment =   $this->countComment($crown['postToken']);
          $array = [

            "postToken" => $crown['postToken'],
            'userid' => ($crown['userid']),
            "username" => $username['username'],
            "article" => $this->validate($crown['article']),
            "likes" => ($crown['likes']),
            "total-comment" => $countComment
          ];

          array_push($dataArray, $array);
        }
        return $dataArray;
      } else {

        $this->outputData(false, 'No post Available', null);
        exit;
      }
    }
  }

  public  function createReply($commentToken,  $reply, $userid, $postToken)
  {

    $time = time();
    $Utlity =  new Utility();

    $escapeData = $this->escapeData($reply);;
    $replyToken = $Utlity->generateAlphaNumericOTP(9);

    $sql = " INSERT INTO tblreply(commentToken, reply, userid, postToken, replyToken)
      VALUES(:commentToken, :reply, :userid,  :postToken, :replyToken)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':commentToken', $commentToken);
    $stmt->bindParam(':reply', $escapeData);
    $stmt->bindParam(':userid', $userid);
    $stmt->bindParam(':commentToken', $commentToken);
    $stmt->bindParam(':postToken', $postToken);
    $stmt->bindParam(':replyToken', $replyToken);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "..";
      $this->outputData(false,  "Something went wrong, please try again", null);
      return false;
    } else {

      $this->outputData(true, 'Reply sent', null);
      return true;
    }
  }

  public function escapeData($string)
  {
    $escaped_string = trim($string);
    $escaped_string = strip_tags($string);
    $escaped_string = htmlspecialchars($string);
    $escaped_string = addslashes($string);

    return $escaped_string;
  }

  // ==============================LIKES FEATURES========================================================================================

  public function verifyCommentLikes($userId, $commentToken)
  {

    $sql = "  SELECT  *  FROM likecomment WHERE userid = '{$userId}' ";
    $sql .= " and   commentToken = '$commentToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
      $count  = $stmt->rowCount();
      if ($count == 1) {
        return true;
      } else {

        return false;
      }
    }
  }

  public function verifyReplyLkes($userId, $replyToken)
  {

    $sql = "  SELECT  *  FROM likereply WHERE userid = '{$userId}' ";
    $sql .= " and   replyToken = '$replyToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
      $count  = $stmt->rowCount();
      if ($count == 1) {
        return true;
      } else {

        return false;
      }
    }
  }

  public function verifyLikesMsg($userId, $postToken)
  {
    global $mysqli;

    $sql = " SELECT  *  FROM likeMessage WHERE userid = '{$userId}' ";
    $sql .= " and  postToken = '$postToken' ";
    $result_set =  mysqli_query($mysqli, $sql);
    $rows  = mysqli_num_rows($result_set);
    if ($rows == 1) {

      return true;
    } else {

      return false;
    }
  }

  public function fetchMergedMsg($userid)
  {
    global $mysqli;
    $dataArray = array();
    $users = new Users();

    $sql  =  " SELECT * FROM message order by id DESC";
    $query  =  mysqli_query($mysqli, $sql);
    while ($value = mysqli_fetch_assoc($query)) {
      $username =   $users->FetchIdByName($value['from_id']);
      $verifyLikedPosts = $this->verifyPostlikes($userid, $value['postToken']);
      // var_dump($verifyLikedPosts);
      // exit;
      $countComment =   $this->countComment($value['postToken']);
      $array = array(

        "postToken" => $value['postToken'],
        "username" => $username['username'],
        "article" => $this->validate($value['article']),
        'userid' => $value['from_id'],
        "likes" => $value['likes'],
        "type" =>  "Message",
        "total-comment" =>  $countComment,
        "verifylike" => $verifyLikedPosts,
        "time" => date("D d M, Y: H", $value['time'])

      );
      array_push($dataArray, $array);
    }
    return $dataArray;
    mysqli_close($mysqli);
  }

  public function fetchMergedPosts($userid)
  {
    global $mysqli;
    $users = new Users();
    $dataArray = array();

    $sql  =  " SELECT * FROM article   order by id  DESC ";
    $query  =  mysqli_query($mysqli, $sql);
    while ($value = mysqli_fetch_assoc($query)) {

      $verifyLikedPosts = $this->verifyPostlikes($userid, $value['postToken']);
      $username =   $users->FetchIdByName($value['userid']);
      $countComment =   $this->countComment($value['postToken']);

      $array = array(
        "postToken" => $value['postToken'],
        "userid" => $value['userid'],
        "article" =>  $this->validate($value['article']),
        "likes" => $value['likes'],
        "username" => $username['username'],
        "verifylike" => $verifyLikedPosts,
        "type" =>  "Article",
        "total-comment" =>  $countComment,
        "time" => date("D d M, Y: H", $value['time'])

      );

      array_push($dataArray, $array);
      # code...
    }
    return $dataArray;
    mysqli_close($mysqli);
  }

  public function countReply($commentToken)
  {

    $sql =  " SELECT  commentToken  FROM  tblreply  WHERE  commentToken = '$commentToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
      $count  = $stmt->rowCount();
      return $count;
    }
  }

  public function authReplylikes($replyToken, $userid)
  {

    if ($this->verifyLikesMsg($userid, $replyToken)  == true) {

      $this->unLikeReplyId($replyToken, $userid);
      exit;
    } else {

      $this->likeCmt($replyToken,  $userid);
      exit;
    }
  }

  public function authCmtlikes($commentToken, $userid)
  {

    if ($this->verifyCmtlikes($userid, $commentToken)  == true) {

      $this->unLikeCmtId($commentToken, $userid);
      exit;
    } else {

      $this->likeCmt($commentToken,  $userid);
      exit;
    }
  }

  // public function authMsglikes($postToken, $userid)
  // {

  //   if ($this->verifyPostlikes($userid, $postToken)  == true) {

  //     $this->unLikeArticleId($postToken, $userid);
  //     exit;
  //   } else {

  //     $this->likeArticle($userid, $postToken);
  //     exit;
  //   }
  // }

  public function authPostlikes($postToken, $userid, $type)
  {

    if ($this->verifyPostlikes($userid, $postToken)  == true) {

      $this->unLikeArticleId($postToken, $userid, $type);
      // exit;
    } else {

      $this->likeArticle($userid, $postToken, $type);
      // exit;
    }
  }

  public function likeReply($replyToken, $userid)
  {

    $sql = " INSERT INTO likereply (replyToken, userid)
      VALUES(:replyToken, :userid) ";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':replyToken', $replyToken);
    $stmt->bindParam(':userid', $userid);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {

      $this->outputData(true,  'Liked', null);
      return true;
    }
  }

  public function likeCmt($commentToken, $userId)
  {

    $sql = " INSERT INTO likecomment (commentToken, userid)
      VALUES(:commentToken, :userid) ";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':commentToken', $commentToken);
    $stmt->bindParam(':userid', $userid);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {

      $this->outputData(true,  'Liked', null);
      return true;
    }
  }

   

  public function likeArticle($userid, $postToken, $type)
  {

    if ($type == "Article") {

      $sql = " INSERT INTO likes (userid, postToken)
        VALUES(:userid, :postToken) ";
      $stmt = $this->connect()->prepare($sql);
      $stmt->bindParam(':userid', $userid);
      $stmt->bindParam(':postToken', $postToken);
      if (!$stmt->execute()) {
        $stmt = null;
        $this->outputData(false,  'Unable to process', null);
        return false;
      } else {

        $articlesql = " UPDATE article SET likes = likes +1";
        $articlesql .=  "  WHERE  postToken = '$postToken' ";
        $stmt = $this->connect()->prepare($articlesql);
        $stmt->execute();

        $this->outputData(true,  'Liked', null);
        return true;
      }
    } else {

      $sql = " INSERT INTO likes (userid, postToken)
        VALUES(:userid, :postToken) ";
      $stmt = $this->connect()->prepare($sql);
      $stmt->bindParam(':userid', $userid);
      $stmt->bindParam(':postToken', $postToken);
      if (!$stmt->execute()) {
        $stmt = null;
        $this->outputData(false,  'Unable to process', null);
        return false;
      } else {

        $msgsql = " UPDATE message SET likes = likes +1";
        $msgsql .=  "  WHERE  postToken = '$postToken' ";
        $stmt = $this->connect()->prepare($msgsql);
        $stmt->execute();

        $this->outputData(true,  'Liked', null);
        return true;
      }
    }
  }

  public function unLikeReplyId($replyToken, $userid)
  {

    $sql = " DELETE FROM likereply WHERE  replyToken = replyToken";
    $sql .= " AND userid = userid ";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':replyToken', $replyToken);
    $stmt->bindParam(':userid', $userid);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {

      $this->outputData(true,  'unliked', null);
      return true;
    }
  }

  public function unLikeCmtId($commentToken, $userid)
  {

    $sql = " DELETE FROM likecomment WHERE  commentToken = commentToken";
    $sql .= " AND userid = userid ";
    $stmt = $this->connect()->prepare($sql);
    $stmt->bindParam(':commentToken', $commentToken);
    $stmt->bindParam(':userid', $userid);
    if (!$stmt->execute()) {
      $stmt = null;
      $this->outputData(false,  'Unable to process', null);
      return false;
    } else {

      $this->outputData(true,  'unliked', null);
      return true;
    }
  }

  public function unLikeArticleId($postToken, $userid, $type)
  {

    if ($type == "Article") {

      $sql = " DELETE FROM likes WHERE  postToken = postToken";
      $sql .= " AND userid = userid ";
      $stmt = $this->connect()->prepare($sql);
      $stmt->bindParam(':postToken', $postToken);
      $stmt->bindParam(':userid', $userid);
      if (!$stmt->execute()) {
        $stmt = null;
        $this->outputData(false,  'Unable to process', null);
        return false;
      } else {

        $sql = " UPDATE article SET likes = likes -1   ";
        $sql .= " WHERE  postToken = '$postToken'  ";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        $this->outputData(true,  'unliked', null);

        exit;
      }
    } else {

      $sql = " DELETE FROM likes WHERE  postToken = postToken";
      $sql .= " AND userid = userid ";
      $stmt = $this->connect()->prepare($sql);
      $stmt->bindParam(':postToken', $postToken);
      $stmt->bindParam(':userid', $userid);
      if (!$stmt->execute()) {
        $stmt = null;
        $this->outputData(false,  'Unable to process', null);
        return false;
      } else {

        $msgsql = " UPDATE message SET likes = likes -1   ";
        $msgsql .= " WHERE  postToken = '$postToken'  ";
        $stmt = $this->connect()->prepare($msgsql);
        $stmt->execute();
        $this->outputData(true,  'unliked', null);
        return true;
      }
    }
  }

  // public function unLikeMsgToken($postToken, $userid)
  // {

  //   $sql = " DELETE FROM likemessage WHERE  postToken = postToken";
  //   $sql .= " AND userid = userid ";
  //   $stmt = $this->connect()->prepare($sql);
  //   $stmt->bindParam(':postToken', $postToken);
  //   $stmt->bindParam(':userid', $userid);
  //   if (!$stmt->execute()) {
  //     $stmt = null;
  //     $this->outputData(false,  'Unable to process', null);
  //     return false;
  //   } else {

  //     $this->outputData(true,  'unliked', null);
  //     return true;
  //   }
  // }

  public function verifyReplylikes($userId, $replyToken)
  {

    $sql = "  SELECT  *  FROM likereply WHERE userid = '{$userId}' ";
    $sql .= " and   replyToken = '$replyToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
      $count  = $stmt->rowCount();
      if ($count == 1) {
        return true;
      } else {

        return false;
      }
    }
  }

  public function verifyCmtlikes($userId, $commentToken)
  {

    $sql = "  SELECT  *  FROM likecomment WHERE userid = '{$userId}' ";
    $sql .= " and   commentToken = '$commentToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
      $count  = $stmt->rowCount();
      if ($count == 1) {
        return true;
      } else {

        return false;
      }
    }
  }

  public function updatePosts($userId, $article,  $postToken)
  {

    $sql = " UPDATE article set aricle  = '$article' WHERE userid = '{$userId}' ";
    $sql .= " and   postToken = '$postToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
     
      $this->outputData(true,  'updated', null);
        return true;
      
    }
  }

  public function updateReply($userId, $reply,  $replyToken)
  {

    $sql = " UPDATE tblreply set reply  = '$reply' WHERE userid = '{$userId}' ";
    $sql .= " and   replyToken = '$replyToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
     
      $this->outputData(true,  'updated', null);
        return true;
      
    }
  }

  public function updateComment($userId, $comment,  $commentToken)
  {

    $sql = " UPDATE comment set comment  = '$comment' WHERE userid = '{$userId}' ";
    $sql .= " and   commentToken = '$commentToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
     
      $this->outputData(true,  'updated', null);
        return true;
      
    }
  }

  public function verifyPostlikes($userId, $postToken)
  {

    $sql = "  SELECT  *  FROM likes WHERE userid = '{$userId}' ";
    $sql .= " and   postToken = '$postToken' ";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
      $stmt = null;
      $_SESSION['err'] = "Something went wrong, please try again..";
      $this->outputData(false,  $_SESSION['err'], null);
      return false;
    } else {
      $count  = $stmt->rowCount();
      if ($count == 1) {
        return true;
      } else {

        return false;
      }
    }
  }

  // output data
  public function outputData($success = null, $message = null, $data = null)
  {
    $arr_output = array(
      'success' => $success,
      'message' => $message,
      'data' => $data,
    );
    echo json_encode($arr_output);
  }
}
