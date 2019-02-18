/**
 * Permet de faire en sorte de toujours avoir le BDE en image si l'image de base n'est pas trouvée
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import Img from 'react-image';
import bdeImage from '../../images/bde.jpg';

const Image = ({ images, unloader, ...props }) => {
	const src = Array.isArray(images) ? images : [images];

	if (!unloader) {
		src.push(bdeImage);
	}

	return <Img loader={<span className="loader large active" />} {...props} src={src} />;
};

export default Image;
