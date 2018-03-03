import Add from './Add.html';

export default class AddComponent {

  constructor(ed) {

    this.default = {
      display: false,
      type: 'fb',
      url: '',
    };

    this.component = new Add({
      target: document.getElementsByTagName(`add-blogcard`)[0],
      data: this.default
    });

    this.component.on('add', e => {
      let addText = `[blog-card href="${this.component.get('url')}" type="${this.component.get('type')}"]`;
      ed.execCommand('mceInsertContent', 0, addText);
      this.reset();
    });

  }

  show(text = '') {
    if (text !== '') {
      this.component.set({url: text});
    }
    this.component.set({display: true});
  }

  reset() {
    this.component.set(this.default);
  }
}
