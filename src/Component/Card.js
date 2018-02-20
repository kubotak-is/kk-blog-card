import Card from './Card.html';

const targets = document.getElementsByTagName('blog-card');

Object.keys(targets).forEach(index => {

  let url = targets[index].getAttribute('href');

  let Component = new Card({
    target: targets[index],
    data: {
      url: url
    }
  });

  fetch(`${location.origin}/wp-json/v1/kkblogcard?url=${url}`)
  .then(response => {
    return response.json();
  }).then(json => {
    console.log(json);
    Component.set({
      loaded: true,
      title: json.title ? json.title : '',
      image: json.image ? json.image : `${location.origin}/wp-content/plugins/kk-blog-card/noimage.png`,
      description: json.description ? json.description : '',
      favicon: json.favicon ? json.favicon : '',
      site_url: json.site_url ? json.site_url : '',
      site_name: json.site_name ? json.site_name : ''
    });
  }).catch(ex => {
    console.log('parsing failed', ex);
    Component.destroy();
  });

});
