import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

export function Nav(props) {
	const { active_nav, hammock, member_id } = props;
	const strings = hammock.strings;
    return (
        <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
            <div className="uk-navbar-left">
                <ul className="uk-navbar-nav hammock-navbar">
                    <li className={active_nav === 'subs' ? 'uk-active' : '' }>
                        <Link to={"/member/" + member_id}><span>{strings.edit.tabs.subs}</span></Link>
                    </li>
                    <li className={active_nav === 'activity' ? 'uk-active' : '' }>
                        <Link to={"/member/" + member_id + "/activity"}><span>{strings.edit.tabs.activity}</span></Link>
                    </li>
                    <li className={active_nav === 'transactions' ? 'uk-active' : '' }>
                        <Link to={"/member/" + member_id + "/transactions"}><span>{strings.edit.tabs.transactions}</span></Link>
                    </li>
                </ul>
            </div>
        </nav>
    );
}