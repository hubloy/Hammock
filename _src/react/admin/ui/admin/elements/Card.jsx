import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function CardUI(props) {
    const { body, title, icon = 'cog', active = false, header = false, attributes = Array(), padding = false, footer = false } = props;
    var data = Array(),
        active_class = active ? 'uk-card-primary' : 'uk-card-default',
        title_class = '';
    for ( var k in attributes ){
        if ( attributes.hasOwnProperty(k) ) {
            data.push(k+"="+attributes[k]);
        }
    }
    data = data.join(" ");
    if ( !padding ) {
        active_class += ' uk-padding-remove';
        title_class = 'uk-padding uk-padding-remove-bottom';
    }
    return (
        
        <div>
            <div className={"uk-card " + active_class +"uk-card-body uk-card-hover"} {...data} >
                <h3 className={"uk-card-title " + title_class} >
                    <span uk-icon={"icon: " +icon +"; ratio: 1.5"}></span> {title}
                    <div className="uk-position-top-right uk-padding-small uk-padding-remove-top">
                        {header}
                    </div>
                </h3>
                <div className="uk-card-body">
                    {body}
                </div>
                {footer &&
                    <div className="uk-card-footer">
                        {footer}
                    </div>
                }
            </div>
        </div>
    );
}