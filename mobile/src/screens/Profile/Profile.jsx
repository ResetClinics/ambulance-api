import React from "react";
import { Layout } from "../../shared";
import { Avatar } from 'react-native-paper';

export const Profile = () => {
  return (
    <Layout>
      <Avatar.Image size={140} source={require('../../../assets/image.webp')} style={styles.root} />
    </Layout>
  );
}

const styles = {
  root: {
    marginLeft: 'auto',
    marginRight: 'auto',
    marginBottom: 16
  }
}
