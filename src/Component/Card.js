import Card from './Card.html';
import NotFound from './NotFound.html';
import cardTypes from '../cardTypes';

const targets = document.getElementsByTagName('blog-card');
const targetType = Array.from(cardTypes.keys());

Object.keys(targets).forEach(index => {

  let url  = targets[index].getAttribute('href');
  let type = targets[index].dataset.type;

  // 未定義のtypeの場合はdefault:fbにする
  if (!targetType.includes(type)) {
    type = "fb";
  }

  let Component = new Card({
    target: targets[index],
    data: {
      url: url,
      type: type,
    }
  });

  fetch(`${location.origin}/wp-json/v1/kkblogcard`, {
    method: 'POST',
    body: JSON.stringify({url: url}),
    headers: new Headers({
      'Content-Type': 'application/json'
    })
  })
  .then(response => {
    if (!response.ok) {
        throw Error(response.statusText);
    }
    return response.json();
  }).then(json => {
    Component.set({
      loaded: true,
      title: json.title ? json.title : '',
      image: json.image ? json.image : noimage,
      description: json.description ? json.description : '',
      favicon: json.favicon ? json.favicon : '',
      site_url: json.site_url ? json.site_url : '',
      site_name: json.site_name ? json.site_name : ''
    });
  }).catch(ex => {
    console.log('parsing failed', ex);
    Component.destroy();
    new NotFound({
      target: targets[index],
      data: {
        url: url,
      }
    });
  });

});

const noimage = `data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAAAAAAZai4+AAAACXBIWXMAABJ0AAASdAHeZh94AAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTAyLTE4VDIyOjMzOjIxKzA5OjAwKYbElQAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOC0wMi0xOFQyMjozMzoyMSswOTowMFjbfCkAAATJSURBVHgB7ZpZU9tIFIWvrMU7xg77kmHKrOFlqvL//0HyNJNiANuQiWEIGK8ISZZsp7s1xkbu61FXGIqpuv1gqU+fvt18Omr7Ae0TvMWWeIubAqBtqTwXokW0VAioeClbREuFgIqXskW0VAioeClbREuFgIqXskW0VAioeClbREuFgIqXskW0VAioeClbREuFgIqXskW0VAioeClbREuFgIqXskW0VAioeClbREuFgIqXskW0VAioeClbREuFgIqXskW0VAioeA0V83Ov+wU+PldervdGI/8TtPSVl6MTrfQT2zK3o8Verv9/e4if4aN/0/bN7HqaQxjctl2wCqvmhMg/kWdG+9oepdaK0LhztfRaQVjshuMNrcxyTvQGN23PWNgYhm/J8LbpJVJLJW1SLHI35yG6Z74G/X77MAPgnXtsous2dsNlnlVpXYz04WNtp9sEffBQKS+y0bu/uMV1mzvv+PXMB/Dv279wEYIzB2D48HC3i66ODgDUEgdZ6F4M6vswqnr61oL28M2vHs/OuCxtGU6lfwlra7pd69+wbfl1KGykRs7VQ51ta1jxze3cqPftUmzrwsluZoL2tV05FH3Jx5xs9Q9zmlbYBBug5WgHS5ZZPDKCu9ki2R0D0hsAS5s6ZNeBoQB7aJQzCT33KwR9gIanHxVN693BiE/udbMHeT25ugd2a7ZYqMzZ1orgkoUhQAdKImHmCrRnKy1ziY2LAyPD/ZDZ2xXBYSVYtwlhJtMl7mzChhjLlWTFuGPu/51mJwYPFkQHCsAzFmkp3md/3/jKetYCn+23L/gQmxO+BmEVD1JD0TL8QcjbbFKefNbTHbiQDDsWDIKZKeMXanzl1lGrw97EcFIQTKYzxYPfQ5293+Ob6HVmjYlhepWxyjSRj3Efu/rnDpj5VCr/B3NMFhclg8mkychEE3dztjXlTD564SP1IDF1ck05Ird1J7nzdJSwNTx2yLAmEmC5v+kR+0x3TuSnvEnohr1uGKCpIfltF96LXQkcemo8vcfdSZYJ0R5baLbibasATf7ag/99HN6w8JzPMAK3wlGE7+w4ZafqPf/Mw5UInX9eE0W5Fm3xtlVKj04bfb91ErAjIk7LQN2FkVO9ZnthJ4cZnLT8/v2fOrDdLpm9s04QtE4Dq4jVipctrXzW/8pLGOV4E7ZOHr/ow1HifeOxulg2dmtejc22tqtsul4+tyu8mLmHZizeKpD8cNvyIl/VvDLW0h+ubd/Mb1ipr+yLFTJHNz3HWFx3gK+XPb7p9A2ruIw/Ku0TVvm/0Fu1/H6sujFpxaqFmq46S2EkezB1RqN2NoBznDdLccxy7sJ3rxn3RX6VbS0a7mnHd+9PByn+WyxGe51sdavh16OxL36I/Pu+XiVbsHD8t+1pycIqeiJEdvo62wIr/LkcWRzvvkq28OWxEdoWRkamEy0ZFUwjWhgZmU60ZFQwjWhhZGQ60ZJRwTSihZGR6URLRgXTiBZGRqYTLRkVTCNaGBmZTrRkVDCNaGFkZDrRklHBNKKFkZHpREtGBdOIFkZGphMtGRVMI1oYGZlOtGRUMI1oYWRkOtGSUcE0ooWRkelES0YF04gWRkamEy0ZFUwjWhgZmU60ZFQwjWhhZGT6G6X1A5rBL681kx5LAAAAAElFTkSuQmCC`;
