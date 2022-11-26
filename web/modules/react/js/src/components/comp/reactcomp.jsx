import React, { Component } from 'react';


export default class ReactComp extends Component {
  constructor(props) {
    super(props);
    this.state = {
      text: 'Hello state',
    };
  }

  toggleText = () => {
    const { text } = this.props;
    const element = document.getElementById(`test-text`);
    element.innerText = (element.innerText === this.state.text) ? text : this.state.text;
  }

  render() {
    const { text } = this.props;
    const { currentLanguage } = drupalSettings.path;
    return (
      <>
        <span>{currentLanguage}</span>
        <button onClick={this.toggleText}><h1 id="test-text">{text}</h1></button>
      </>
    );
  }
}