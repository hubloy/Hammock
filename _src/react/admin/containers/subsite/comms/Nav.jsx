import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function Nav(props) {
	const { hammock } = props;
    return (
        <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
            <div className="uk-navbar-left">
                <ul className="uk-navbar-nav hammock-navbar" uk-switcher="connect: .hammock-comms">
                    <li>
                        <a href="#"><span>{hammock.strings.tabs.admin}</span></a>
                    </li>
                    <li>
                        <a href="#"><span>{hammock.strings.tabs.user}</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    );
}