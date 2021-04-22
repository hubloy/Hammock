import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function ModalUI(props) {
    const { id, title, content } = props;
    return(
        <div id={id} className={id} uk-modal="bg-close:false">
            <div className="uk-modal-dialog">
                <button className="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                <div className="uk-modal-header">
                    <h2 className="uk-modal-title">{title}</h2>
                    <div className="uk-invisible hammock-message uk-padding-remove uk-margin-remove" uk-alert></div>
                </div>
                <div className="uk-modal-body">{content}</div>
            </div>
        </div>
    )
}