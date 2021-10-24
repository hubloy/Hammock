import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

export function Nav(props) {
	const strings = props.hammock.strings;
    return (
        <div className="uk-width-1-4 uk-height-medium">
            <h2 className="uk-heading-divider">{strings.edit.title}</h2>
            <Link className="uk-border-rounded uk-margin-bottom uk-background-default uk-button uk-button-default uk-button-small" to="/">{strings.edit.back}</Link>
            <ul className="uk-nav-default hammock-switcher-nav uk-nav-parent-icon" uk-nav="" uk-switcher="connect: .hammock-membership">
                <li>
                    <a href="#" className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{strings.edit.tabs.general}</span></a>
                </li>
                <li>
                    <a href="#" className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{strings.edit.tabs.rules}</span></a>
                </li>
                <li>
                    <a href="#" className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{strings.edit.tabs.price}</span></a>
                </li>
            </ul>
        </div>
    );
}