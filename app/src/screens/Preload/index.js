import React from 'react';
import { Text } from 'react-native';
import { Container } from './styles';

import AppLogo from '../../assets/logo.svg';


export default () => {
    return (
        <Container>
            <AppLogo width={100} height={100} />
        </Container>
    );
}