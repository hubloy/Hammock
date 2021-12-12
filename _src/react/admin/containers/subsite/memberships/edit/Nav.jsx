import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

export function Nav(props) {
	var strings = props.hammock.strings,
    active_nav = props.active_nav,
    id = props.id;
    return (
        <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
            <div className="uk-navbar-left">
                <ul className="uk-navbar-nav hammock-navbar">
                    <li className={active_nav === 'general' ? 'uk-active' : '' }>
                        <Link to={"/edit/" + id}><span>{strings.edit.tabs.general}</span></Link>
                    </li>
                    <li className={active_nav === 'price' ? 'uk-active' : '' }>
                        <Link to={"/edit/" + id + "/price"}><span>{strings.edit.tabs.price}</span></Link>
                    </li>
                </ul>
            </div>
        </nav>
    );
}