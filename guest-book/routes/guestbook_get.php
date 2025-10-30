<?php

$messages = getMessages(connectDb());

//echo $hey;
//throw new RuntimeException('Not implemented');
renderView('guestbook_get',
data:['messages'=>$messages]

);