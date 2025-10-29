<?php

$messages = getMessages(connectDb());
renderView('guestbook_get',
data:['messages'=>$messages]

);