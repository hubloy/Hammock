import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import Dashboard from 'layout/Dashboard'
import Nav from './rules/Nav';

export default class Rules extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hammock = this.props.hammock;
		return (
			<Dashboard hammock={hammock}>
				<div className="uk-child-width-expand hammock-rules" uk-grid="">
                    <div className="hammock-grid-left uk-width-1-4">
                        <Nav hammock={hammock} />
                    </div>
                    <div className="hammock-grid-right uk-width-expand">

                    </div>
                </div>
			</Dashboard>
		)
	}
}

Rules.propTypes = {
	hammock: PropTypes.object
};