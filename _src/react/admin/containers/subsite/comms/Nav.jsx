import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function Nav(props) {
	const { hubloy_membership } = props;
    return (
        <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
            <div className="uk-navbar-left">
                <ul className="uk-navbar-nav hubloy_membership-navbar" uk-switcher="connect: .hubloy_membership-comms">
                    <li>
                        <a href="#"><span>{hubloy_membership.strings.tabs.admin}</span></a>
                    </li>
                    <li>
                        <a href="#"><span>{hubloy_membership.strings.tabs.user}</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    );
}