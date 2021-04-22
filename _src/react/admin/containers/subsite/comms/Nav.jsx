import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function Nav(props) {
	const { hammock } = props;
    return (
        <div className="uk-width-1-4 uk-height-medium">
            <h2 className="uk-heading-divider">{hammock.common.string.title}</h2>
            <ul className="uk-nav-default hammock-switcher-nav uk-nav-parent-icon" uk-nav="" uk-switcher="connect: .hammock-comms">
                <li>
                    <a href="#" className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{hammock.strings.tabs.admin}</span></a>
                </li>
                <li>
                    <a href="#" className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{hammock.strings.tabs.user}</span></a>
                </li>
            </ul>
        </div>
    );
}