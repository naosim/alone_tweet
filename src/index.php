<?php
include_once "./php/infra/AppAuth.php";
// AppAuth::auth();
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>

<div class="main">
  <ul>
  </ul>
</div>

<div id="app">
  <div class="new_article">
    <textarea v-model="data.new_article.body">
    </textarea>
    <button id="send_new_article_button">send</button>
  </div>
  <ul>
    <li v-for="v in data.list">
      <!--{{ v.id }}-->
      {{ v.body }}
      {{ v.publish_datetime.ISO8601.slice(0, 16) }}
    </li>
  </ul>
</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/vue"></script>

<script>
// axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';

var globalData = {
  list: [],
  new_article: {
    body: ''
  },
};

var app = new Vue({
  el: '#app',
  data: { data: globalData }
});

var reloadList = () => {
  axios.get('articles.php')
    .then(function (response) {
      console.log(response);
      globalData.list = response.data.map(v => {
        v.body = '';
        return v;
      });

      globalData.list
        .filter(v => !v.body)
        .forEach(v => {
          axios.get(`articles.php?id=${v.id}`)
            .then(function (response) {
              v.body = response.data.body;
            })
            .catch(function (error) {
              console.log(error);
            });
        });

    })
    .catch(function (error) {
      console.log(error);
    });
}
reloadList();

document.querySelector('#send_new_article_button').addEventListener('click', () => {
  if(!globalData.new_article.body || globalData.new_article.body.trim().length == 0) {
    return;
  }
  // body
  // publish_datetime_unix
  var params = new URLSearchParams();
  params.append('body', globalData.new_article.body);
  params.append('publish_datetime_unix', ("" + Date.now()).slice(0, 10));
  axios.post('articles_create.php', params)
  .then(function (response) {
    console.log(response);
    if(response.status == 200) {
      globalData.new_article.body = '';
      reloadList();
    }
  })
  .catch(function (error) {
    console.log(error);
  });
});
</script>
