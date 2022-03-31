import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

export default class Table extends Component {

	render() {
		const { hubloy_membership, items, type } = this.props;
		var strings = hubloy_membership.strings;
		return (
			<table className="uk-table">
				<thead>
					<tr>
						<th className="hubloy_membership-email-status">{strings.table.status}</th>
						<th>{strings.table.email}</th>
						<th>{strings.table.description}</th>
						<th>{strings.table.recipient}</th>
						<th className="hubloy_membership-email-manage"></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th className="hubloy_membership-email-status">{strings.table.status}</th>
						<th>{strings.table.email}</th>
						<th>{strings.table.description}</th>
						<th>{strings.table.recipient}</th>
						<th className="hubloy_membership-email-manage"></th>
					</tr>
				</tfoot>
				<tbody>
					{Object.keys(items).map(item =>
						<tr key={items[item].id}>
							<td><span className={items[item].settings.enabled ? 'uk-text-success' : 'uk-text-warning'} title={items[item].settings.enabled ? hubloy_membership.common.status.enabled : hubloy_membership.common.status.disabled} uk-tooltip={items[item].settings.enabled ? hubloy_membership.common.status.enabled : hubloy_membership.common.status.disabled} uk-icon={items[item].settings.enabled ? 'check' : 'close'}></span></td>
							<td>{items[item].settings.title}</td>
							<td>{items[item].settings.description}</td>
							<td>{type === 'member' ? strings.table.customer : items[item].settings.recipient}</td>
							<td>
								<Link className="uk-button uk-button-default uk-button-small" to={"/manage/" + items[item].id} title={hubloy_membership.common.buttons.manage}>{hubloy_membership.common.buttons.manage}</Link>
							</td>
						</tr>
					)}
				</tbody>
			</table>
		)
	}
}